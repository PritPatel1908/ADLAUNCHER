<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleMedia;
use App\Models\Device;
use App\Models\DeviceLayout;
use App\Models\DeviceScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'schedule_name' => 'required|string|max:255',
            'schedule_start_date_time' => 'required|date',
            'schedule_end_date_time' => 'required|date|after_or_equal:schedule_start_date_time',
            'device_id' => 'required|exists:devices,id',
            'layout_id' => 'nullable|exists:device_layouts,id',
            'screen_id' => 'nullable|exists:device_screens,id',
            'media_title.*' => 'nullable|string|max:255',
            'media_type.*' => 'nullable|string|in:image,video,audio,mp4,png,jpg,pdf',
            'duration_seconds.*' => 'nullable|integer|min:1|sometimes',
            'media_file.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,mp3,wav,pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $schedule = Schedule::create([
                'schedule_name' => $request->schedule_name,
                'schedule_start_date_time' => $request->schedule_start_date_time,
                'schedule_end_date_time' => $request->schedule_end_date_time,
                'device_id' => $request->device_id,
                'layout_id' => $request->layout_id,
                'screen_id' => $request->screen_id,
            ]);

            // Handle ScheduleMedia creation
            if ($request->has('media_title')) {
                $mediaTitles = $request->input('media_title', []);
                $mediaTypes = $request->input('media_type', []);
                $durationSeconds = $request->input('duration_seconds', []);
                $mediaFiles = $request->file('media_file', []);

                // Clean up empty duration values
                $durationSeconds = array_map(function ($duration) {
                    return empty($duration) ? null : (int)$duration;
                }, $durationSeconds);

                for ($i = 0; $i < count($mediaTitles); $i++) {
                    if (!empty($mediaTitles[$i]) || !empty($mediaTypes[$i])) {
                        $mediaData = [
                            'schedule_id' => $schedule->id,
                            'title' => $mediaTitles[$i] ?? null,
                            'media_type' => $mediaTypes[$i] ?? null,
                            'duration_seconds' => $durationSeconds[$i] ?? null,
                        ];

                        // Handle file upload
                        if (isset($mediaFiles[$i]) && $mediaFiles[$i]->isValid()) {
                            $file = $mediaFiles[$i];
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs('schedule_media', $filename, 'public');
                            $mediaData['media_file'] = $path;
                        }

                        ScheduleMedia::create($mediaData);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'schedule' => $schedule->load(['device', 'layout', 'screen', 'medias'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
        $schedule->load(['device', 'layout', 'screen', 'medias']);
        return view('schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load(['device', 'layout', 'screen', 'medias']);
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
            'schedule_start_date_time' => 'required|date',
            'schedule_end_date_time' => 'required|date|after_or_equal:schedule_start_date_time',
            'device_id' => 'required|exists:devices,id',
            'layout_id' => 'nullable|exists:device_layouts,id',
            'screen_id' => 'nullable|exists:device_screens,id',
            'edit_media_title.*' => 'nullable|string|max:255',
            'edit_media_type.*' => 'nullable|string|in:image,video,audio,mp4,png,jpg,pdf',
            'edit_duration_seconds.*' => 'nullable|integer|min:1|sometimes',
            'edit_media_file.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,mp3,wav,pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $schedule->update([
                'schedule_name' => $request->schedule_name,
                'schedule_start_date_time' => $request->schedule_start_date_time,
                'schedule_end_date_time' => $request->schedule_end_date_time,
                'device_id' => $request->device_id,
                'layout_id' => $request->layout_id,
                'screen_id' => $request->screen_id,
            ]);

            // Handle ScheduleMedia updates
            if ($request->has('edit_media_title')) {
                $mediaTitles = $request->input('edit_media_title', []);
                $mediaTypes = $request->input('edit_media_type', []);
                $durationSeconds = $request->input('edit_duration_seconds', []);
                $mediaFiles = $request->file('edit_media_file', []);
                $mediaIds = $request->input('edit_media_id', []);

                // Clean up empty duration values
                $durationSeconds = array_map(function ($duration) {
                    return empty($duration) ? null : (int)$duration;
                }, $durationSeconds);

                for ($i = 0; $i < count($mediaTitles); $i++) {
                    if (!empty($mediaTitles[$i]) || !empty($mediaTypes[$i])) {
                        $mediaData = [
                            'title' => $mediaTitles[$i] ?? null,
                            'media_type' => $mediaTypes[$i] ?? null,
                            'duration_seconds' => $durationSeconds[$i] ?? null,
                        ];

                        // Handle file upload
                        if (isset($mediaFiles[$i]) && $mediaFiles[$i]->isValid()) {
                            $file = $mediaFiles[$i];
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs('schedule_media', $filename, 'public');
                            $mediaData['media_file'] = $path;
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

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'schedule' => $schedule->load(['device', 'layout', 'screen', 'medias'])
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
        $query = Schedule::with(['device', 'layout', 'screen']);

        // Filters
        if ($request->filled('name_filter')) {
            $query->where('schedule_name', 'like', '%' . $request->name_filter . '%');
        }
        if ($request->filled('device_filter')) {
            $query->where('device_id', $request->device_filter);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('schedule_start_date_time', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $query->orderBy('schedule_start_date_time', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('schedule_start_date_time', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('schedule_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('schedule_name', 'desc');
                    break;
                default:
                    $query->orderBy('schedule_start_date_time', 'desc');
            }
        } else {
            $query->orderBy('schedule_start_date_time', 'desc');
        }

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);

        $recordsTotal = (clone $query)->count();

        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('schedule_name', 'like', "%$searchValue%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $schedules = $query
            ->skip($start)
            ->take($length)
            ->get();

        $data = $schedules->map(function ($s) {
            return [
                'id' => $s->id,
                'schedule_name' => $s->schedule_name,
                'device' => $s->device ? ($s->device->name ?? $s->device->unique_id) : null,
                'layout' => $s->layout ? $s->layout->layout_name : null,
                'screen' => $s->screen ? $s->screen->screen_no : null,
                'start_at' => $s->schedule_start_date_time,
                'end_at' => $s->schedule_end_date_time,
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
