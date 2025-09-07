<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Schedule;
use App\Models\DeviceLayout;
use App\Models\DeviceScreen;
use Illuminate\Http\Request;
use App\Models\ScheduleMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('schedule.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Schedule creation request received', [
            'all_data' => $request->all(),
            'files' => $request->allFiles(),
            'has_schedule_name' => $request->has('schedule_name'),
            'has_device_id' => $request->has('device_id'),
            'has_start_date' => $request->has('schedule_start_date_time'),
            'has_end_date' => $request->has('schedule_end_date_time'),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
        ]);

        try {
            $request->validate([
                'schedule_name' => 'required|string|max:255',
                'device_id' => 'required|exists:devices,id',
                'layout_id' => 'nullable|exists:device_layouts,id',
                'schedule_start_date_time' => 'nullable|date',
                'schedule_end_date_time' => 'nullable|date',
                'play_forever' => 'nullable|boolean',
                'media_title.*' => 'nullable|string|max:255',
                'media_type.*' => 'nullable|string|in:image,video,audio,mp4,png,jpg,pdf',
                'media_file.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,mp3,wav,pdf|max:204800', // 200MB limit
                'media_screen_id.*' => 'nullable|exists:device_screens,id',
                'media_start_date_time.*' => 'nullable|date',
                'media_end_date_time.*' => 'nullable|date',
                'media_play_forever.*' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Schedule validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'files' => $request->allFiles()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $schedule = Schedule::create([
                'schedule_name' => $request->schedule_name,
                'device_id' => $request->device_id,
                'layout_id' => $request->layout_id,
            ]);

            // Parse schedule-level defaults
            $scheduleLevelStart = null;
            $scheduleLevelEnd = null;
            if (!empty($request->schedule_start_date_time)) {
                try {
                    $scheduleLevelStart = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->schedule_start_date_time);
                } catch (\Exception $e) {
                    try {
                        $scheduleLevelStart = \Carbon\Carbon::parse($request->schedule_start_date_time);
                    } catch (\Exception $e2) {
                        $scheduleLevelStart = null;
                    }
                }
            }
            if (!empty($request->schedule_end_date_time)) {
                try {
                    $scheduleLevelEnd = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->schedule_end_date_time);
                } catch (\Exception $e) {
                    try {
                        $scheduleLevelEnd = \Carbon\Carbon::parse($request->schedule_end_date_time);
                    } catch (\Exception $e2) {
                        $scheduleLevelEnd = null;
                    }
                }
            }
            $scheduleLevelForever = (bool) $request->boolean('play_forever');

            // Handle ScheduleMedia creation
            if ($request->has('media_title')) {
                $mediaTitles = $request->input('media_title', []);
                $mediaTypes = $request->input('media_type', []);
                $mediaFiles = $request->file('media_file', []);
                $mediaScreenIds = $request->input('media_screen_id', []);
                $mediaStarts = $request->input('media_start_date_time', []);
                $mediaEnds = $request->input('media_end_date_time', []);
                $mediaForever = $request->input('media_play_forever', []);

                for ($i = 0; $i < count($mediaTitles); $i++) {
                    // Create media if any of title, type, or file is provided
                    if (!empty($mediaTitles[$i]) || !empty($mediaTypes[$i]) || (isset($mediaFiles[$i]) && $mediaFiles[$i]->isValid())) {
                        $startAt = null;
                        $endAt = null;
                        if (!empty($mediaStarts[$i])) {
                            try {
                                $startAt = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $mediaStarts[$i]);
                            } catch (\Exception $e) {
                                $startAt = null;
                            }
                        }
                        if (!empty($mediaEnds[$i])) {
                            try {
                                $endAt = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $mediaEnds[$i]);
                            } catch (\Exception $e) {
                                $endAt = null;
                            }
                        }

                        // Fallbacks to schedule-level values when per-media ones are absent
                        if ($startAt === null) {
                            $startAt = $scheduleLevelStart;
                        }
                        if ($endAt === null) {
                            $endAt = $scheduleLevelEnd;
                        }
                        $playForeverVal = isset($mediaForever[$i]) && $mediaForever[$i] !== null && $mediaForever[$i] !== ''
                            ? (!empty($mediaForever[$i]) ? true : false)
                            : $scheduleLevelForever;

                        $mediaData = [
                            'schedule_id' => $schedule->id,
                            'title' => $mediaTitles[$i] ?? null,
                            'media_type' => $mediaTypes[$i] ?? null,
                            'screen_id' => $mediaScreenIds[$i] ?? null,
                            'schedule_start_date_time' => $startAt,
                            'schedule_end_date_time' => $endAt,
                            'play_forever' => $playForeverVal,
                        ];

                        // Handle file upload
                        if (isset($mediaFiles[$i]) && $mediaFiles[$i]->isValid()) {
                            $file = $mediaFiles[$i];
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs('schedule_media', $filename, 'public');
                            $mediaData['media_file'] = $path;
                        } elseif (isset($mediaFiles[$i])) {
                            // Log file upload errors for debugging
                            Log::error('File upload failed', [
                                'file_name' => $mediaFiles[$i]->getClientOriginalName(),
                                'file_size' => $mediaFiles[$i]->getSize(),
                                'file_mime' => $mediaFiles[$i]->getMimeType(),
                                'errors' => $mediaFiles[$i]->getError(),
                                'error_message' => $mediaFiles[$i]->getErrorMessage()
                            ]);
                        }

                        ScheduleMedia::create($mediaData);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'schedule' => $schedule->load(['device', 'layout', 'medias.screen'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'files' => $request->allFiles()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['device', 'layout', 'medias.screen']);

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json([
                'schedule' => $schedule
            ]);
        }

        return view('schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load(['device', 'layout', 'medias.screen']);
        return response()->json([
            'success' => true,
            'schedule' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'schedule_name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'layout_id' => 'nullable|exists:device_layouts,id',
            'schedule_start_date_time' => 'nullable|date',
            'schedule_end_date_time' => 'nullable|date',
            'play_forever' => 'nullable|boolean',
            'edit_media_title.*' => 'nullable|string|max:255',
            'edit_media_type.*' => 'nullable|string|in:image,video,audio,mp4,png,jpg,pdf',
            'edit_media_file.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,mp3,wav,pdf|max:204800', // 200MB limit
            'edit_media_screen_id.*' => 'nullable|exists:device_screens,id',
            'edit_media_start_date_time.*' => 'nullable|date',
            'edit_media_end_date_time.*' => 'nullable|date',
            'edit_media_play_forever.*' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $schedule->update([
                'schedule_name' => $request->schedule_name,
                'device_id' => $request->device_id,
                'layout_id' => $request->layout_id,
            ]);

            // Parse schedule-level defaults
            $scheduleLevelStart = null;
            $scheduleLevelEnd = null;
            if (!empty($request->schedule_start_date_time)) {
                try {
                    $scheduleLevelStart = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->schedule_start_date_time);
                } catch (\Exception $e) {
                    try {
                        $scheduleLevelStart = \Carbon\Carbon::parse($request->schedule_start_date_time);
                    } catch (\Exception $e2) {
                        $scheduleLevelStart = null;
                    }
                }
            }
            if (!empty($request->schedule_end_date_time)) {
                try {
                    $scheduleLevelEnd = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->schedule_end_date_time);
                } catch (\Exception $e) {
                    try {
                        $scheduleLevelEnd = \Carbon\Carbon::parse($request->schedule_end_date_time);
                    } catch (\Exception $e2) {
                        $scheduleLevelEnd = null;
                    }
                }
            }
            $scheduleLevelForever = (bool) $request->boolean('play_forever');

            // Handle ScheduleMedia updates
            if ($request->has('edit_media_title')) {
                $mediaTitles = $request->input('edit_media_title', []);
                $mediaTypes = $request->input('edit_media_type', []);
                $mediaFiles = $request->file('edit_media_file', []);
                $mediaIds = $request->input('edit_media_id', []);
                $mediaScreenIds = $request->input('edit_media_screen_id', []);
                $mediaStarts = $request->input('edit_media_start_date_time', []);
                $mediaEnds = $request->input('edit_media_end_date_time', []);
                $mediaForever = $request->input('edit_media_play_forever', []);

                for ($i = 0; $i < count($mediaTitles); $i++) {
                    if (!empty($mediaTitles[$i]) || !empty($mediaTypes[$i])) {
                        $startAt = null;
                        $endAt = null;
                        if (!empty($mediaStarts[$i])) {
                            try {
                                $startAt = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $mediaStarts[$i]);
                            } catch (\Exception $e) {
                                $startAt = null;
                            }
                        }
                        if (!empty($mediaEnds[$i])) {
                            try {
                                $endAt = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $mediaEnds[$i]);
                            } catch (\Exception $e) {
                                $endAt = null;
                            }
                        }

                        // Fallbacks to schedule-level values when per-media ones are absent
                        if ($startAt === null) {
                            $startAt = $scheduleLevelStart;
                        }
                        if ($endAt === null) {
                            $endAt = $scheduleLevelEnd;
                        }
                        $playForeverVal = isset($mediaForever[$i]) && $mediaForever[$i] !== null && $mediaForever[$i] !== ''
                            ? (!empty($mediaForever[$i]) ? true : false)
                            : $scheduleLevelForever;

                        $mediaData = [
                            'title' => $mediaTitles[$i] ?? null,
                            'media_type' => $mediaTypes[$i] ?? null,
                            'screen_id' => $mediaScreenIds[$i] ?? null,
                            'schedule_start_date_time' => $startAt,
                            'schedule_end_date_time' => $endAt,
                            'play_forever' => $playForeverVal,
                        ];

                        // Handle file upload
                        if (isset($mediaFiles[$i]) && $mediaFiles[$i]->isValid()) {
                            $file = $mediaFiles[$i];
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs('schedule_media', $filename, 'public');
                            $mediaData['media_file'] = $path;
                        } elseif (isset($mediaFiles[$i])) {
                            // Log file upload errors for debugging
                            Log::error('File upload failed in update', [
                                'file_name' => $mediaFiles[$i]->getClientOriginalName(),
                                'file_size' => $mediaFiles[$i]->getSize(),
                                'file_mime' => $mediaFiles[$i]->getMimeType(),
                                'errors' => $mediaFiles[$i]->getError(),
                                'error_message' => $mediaFiles[$i]->getErrorMessage()
                            ]);
                        }

                        // Update existing media or create new one
                        if (!empty($mediaIds[$i])) {
                            $media = ScheduleMedia::find($mediaIds[$i]);
                            if ($media) {
                                $media->update($mediaData);
                            }
                        } else {
                            $mediaData['schedule_id'] = $schedule->id;
                            ScheduleMedia::create($mediaData);
                        }
                    }
                }
            }

            // Handle media deletions
            if ($request->has('delete_media_ids')) {
                $deleteIds = $request->input('delete_media_ids', []);
                ScheduleMedia::whereIn('id', $deleteIds)->delete();
            }

            DB::commit();

            // Load the schedule with relationships
            $schedule->load(['device', 'layout', 'medias.screen']);

            // Add formatted dates from first media to match the blade template format
            $firstMedia = $schedule->medias->first();
            $schedule->formatted_start_date = $firstMedia && $firstMedia->schedule_start_date_time ? \Carbon\Carbon::parse($firstMedia->schedule_start_date_time)->format('d M Y, h:i A') : null;
            $schedule->formatted_end_date = $firstMedia && $firstMedia->schedule_end_date_time ? \Carbon\Carbon::parse($firstMedia->schedule_end_date_time)->format('d M Y, h:i A') : null;
            $schedule->formatted_created_date = $schedule->created_at->format('d M Y, h:i A');

            // Format media created dates
            if ($schedule->medias) {
                foreach ($schedule->medias as $media) {
                    $media->formatted_created_date = $media->created_at ? $media->created_at->format('d M Y, h:i A') : 'N/A';
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule update error: ' . $e->getMessage(), [
                'schedule_id' => $schedule->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();
            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get devices data for DataTables
     */
    public function getData(Request $request)
    {
        // Build a base query WITHOUT grouping to stay compatible with SQL Server
        $baseQuery = Schedule::with(['device', 'layout', 'medias.screen'])
            ->select('schedules.*');

        // Filters
        if ($request->filled('name_filter')) {
            $baseQuery->where('schedule_name', 'like', '%' . $request->name_filter . '%');
        }
        if ($request->filled('device_filter')) {
            $baseQuery->where('device_id', $request->device_filter);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Filter schedules that have at least one media in the date range
            $start = $request->start_date;
            $end = $request->end_date . ' 23:59:59';
            $baseQuery->whereExists(function ($q) use ($start, $end) {
                $q->from('schedule_medias as sm')
                    ->whereColumn('sm.schedule_id', 'schedules.id')
                    ->whereBetween('sm.schedule_start_date_time', [$start, $end]);
            });
        }

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);

        // Count distinct schedules to avoid over-counting due to the join
        $recordsTotal = (clone $baseQuery)->distinct('schedules.id')->count('schedules.id');

        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $baseQuery->where(function ($q) use ($searchValue) {
                $q->where('schedule_name', 'like', "%$searchValue%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->distinct('schedules.id')->count('schedules.id');

        // For sorting by media start time, join a subquery that aggregates per-schedule
        $aggSub = DB::table('schedule_medias')
            ->select('schedule_id', DB::raw('MAX(schedule_start_date_time) as latest_start'))
            ->groupBy('schedule_id');

        $dataQuery = (clone $baseQuery)
            ->leftJoinSub($aggSub, 'smagg', function ($join) {
                $join->on('schedules.id', '=', 'smagg.schedule_id');
            });

        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $dataQuery->orderBy('smagg.latest_start', 'desc');
                    break;
                case 'oldest':
                    $dataQuery->orderBy('smagg.latest_start', 'asc');
                    break;
                case 'name_asc':
                    $dataQuery->orderBy('schedule_name', 'asc');
                    break;
                case 'name_desc':
                    $dataQuery->orderBy('schedule_name', 'desc');
                    break;
                default:
                    $dataQuery->orderBy('smagg.latest_start', 'desc');
            }
        } else {
            $dataQuery->orderBy('smagg.latest_start', 'desc');
        }

        $schedules = $dataQuery
            ->skip($start)
            ->take($length)
            ->get();

        $data = $schedules->map(function ($s) {
            return [
                'id' => $s->id,
                'schedule_name' => $s->schedule_name,
                'device' => $s->device ? ($s->device->name ?? $s->device->unique_id) : null,
                'layout' => $s->layout ? $s->layout->layout_name : null,
                'created_at' => $s->created_at,
                'updated_at' => $s->updated_at,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created device layout.
     */
    public function storeLayout(Request $request)
    {
        $request->validate([
            'layout_name' => 'required|string|max:255',
            'layout_type' => 'required|integer|in:0,1,2,3',
            'device_id' => 'required|exists:devices,id',
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        try {
            $deviceLayout = DeviceLayout::create([
                'layout_name' => $request->layout_name,
                'layout_type' => $request->layout_type,
                'device_id' => $request->device_id,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device layout created successfully',
                'layout' => $deviceLayout
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create device layout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified device layout.
     */
    public function updateLayout(Request $request, DeviceLayout $deviceLayout)
    {
        $request->validate([
            'layout_name' => 'required|string|max:255',
            'layout_type' => 'required|integer|in:0,1,2,3',
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        try {
            $deviceLayout->update([
                'layout_name' => $request->layout_name,
                'layout_type' => $request->layout_type,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device layout updated successfully',
                'layout' => $deviceLayout
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update device layout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified device layout.
     */
    public function destroyLayout(DeviceLayout $deviceLayout)
    {
        try {
            $deviceLayout->delete();
            return response()->json([
                'success' => true,
                'message' => 'Device layout deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete device layout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all device layouts.
     */
    public function getLayouts()
    {
        try {
            $layouts = DeviceLayout::with('device')->get();
            return response()->json([
                'success' => true,
                'layouts' => $layouts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load device layouts: ' . $e->getMessage()
            ], 500);
        }
    }
}
