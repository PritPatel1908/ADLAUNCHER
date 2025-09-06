<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceScreen;
use App\Models\DeviceLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $screens = DeviceScreen::with(['device', 'layout'])->get();
        return response()->json([
            'success' => true,
            'screens' => $screens,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'screen_no' => 'required|integer|min:1|max:255',
            'screen_height' => 'required|integer|min:1',
            'screen_width' => 'required|integer|min:1',
            'device_id' => 'required|exists:devices,id',
            'layout_id' => 'required|exists:device_layouts,id',
        ]);

        try {
            DB::beginTransaction();

            // Get the layout to check its type and screen limits
            $layout = DeviceLayout::findOrFail($request->layout_id);
            
            // Check if layout allows adding more screens
            if (!$layout->canAddMoreScreens()) {
                return response()->json([
                    'success' => false,
                    'message' => "This layout type ({$layout->layout_type_name}) allows maximum {$layout->max_screens} screen(s). You have already reached the limit."
                ], 422);
            }

            // Ensure screen_no is unique per device
            $exists = DeviceScreen::where('device_id', $request->device_id)
                ->where('screen_no', $request->screen_no)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen number already exists for this device.'
                ], 422);
            }

            // Check for dimension conflicts (same height and width in same device)
            $deviceScreen = new DeviceScreen();
            if ($deviceScreen->hasDimensionConflict($request->screen_height, $request->screen_width, $request->device_id)) {
                $conflictingScreens = $deviceScreen->getConflictingScreens($request->screen_height, $request->screen_width, $request->device_id);
                $conflictInfo = $conflictingScreens->pluck('screen_no')->join(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Screen dimensions ({$request->screen_height}x{$request->screen_width}) already exist for screen(s): {$conflictInfo}. Please use different dimensions."
                ], 422);
            }

            $screen = DeviceScreen::create([
                'screen_no' => $request->screen_no,
                'screen_height' => $request->screen_height,
                'screen_width' => $request->screen_width,
                'device_id' => $request->device_id,
                'layout_id' => $request->layout_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device screen created successfully',
                'screen' => $screen->load(['device', 'layout']),
                'layout_info' => [
                    'max_screens' => $layout->max_screens,
                    'remaining_slots' => $layout->remaining_screen_slots,
                    'layout_type' => $layout->layout_type_name
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create device screen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceScreen $deviceScreen)
    {
        $deviceScreen->load(['device', 'layout']);
        return response()->json([
            'success' => true,
            'screen' => $deviceScreen,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceScreen $deviceScreen)
    {
        $request->validate([
            'screen_no' => 'required|integer|min:1|max:255',
            'screen_height' => 'required|integer|min:1',
            'screen_width' => 'required|integer|min:1',
            'layout_id' => 'required|exists:device_layouts,id',
        ]);

        try {
            DB::beginTransaction();

            // Get the layout to check its type and screen limits
            $layout = DeviceLayout::findOrFail($request->layout_id);
            
            // If changing layout, check if new layout allows this screen
            if ($deviceScreen->layout_id != $request->layout_id) {
                // Count screens in the new layout (excluding current screen)
                $screensInNewLayout = DeviceScreen::where('layout_id', $request->layout_id)
                    ->where('id', '!=', $deviceScreen->id)
                    ->count();
                
                if ($screensInNewLayout >= $layout->max_screens) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot move screen to this layout. Layout type ({$layout->layout_type_name}) allows maximum {$layout->max_screens} screen(s) and already has {$screensInNewLayout} screen(s)."
                    ], 422);
                }
            }

            // Ensure screen_no uniqueness per device when updating
            $exists = DeviceScreen::where('device_id', $deviceScreen->device_id)
                ->where('screen_no', $request->screen_no)
                ->where('id', '!=', $deviceScreen->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screen number already exists for this device.'
                ], 422);
            }

            // Check for dimension conflicts (same height and width in same device, excluding current screen)
            if ($deviceScreen->hasDimensionConflict($request->screen_height, $request->screen_width, $deviceScreen->device_id, $deviceScreen->id)) {
                $conflictingScreens = $deviceScreen->getConflictingScreens($request->screen_height, $request->screen_width, $deviceScreen->device_id, $deviceScreen->id);
                $conflictInfo = $conflictingScreens->pluck('screen_no')->join(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Screen dimensions ({$request->screen_height}x{$request->screen_width}) already exist for screen(s): {$conflictInfo}. Please use different dimensions."
                ], 422);
            }

            $deviceScreen->update([
                'screen_no' => $request->screen_no,
                'screen_height' => $request->screen_height,
                'screen_width' => $request->screen_width,
                'layout_id' => $request->layout_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device screen updated successfully',
                'screen' => $deviceScreen->load(['device', 'layout']),
                'layout_info' => [
                    'max_screens' => $layout->max_screens,
                    'remaining_slots' => $layout->remaining_screen_slots,
                    'layout_type' => $layout->layout_type_name
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update device screen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceScreen $deviceScreen)
    {
        try {
            $deviceScreen->delete();
            return response()->json([
                'success' => true,
                'message' => 'Device screen deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete device screen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get screens for a specific device
     */
    public function getDeviceScreens(Device $device)
    {
        $query = $device->deviceScreens()->with('layout')->orderBy('screen_no');
        // Optional filter by layout_id when provided
        $layoutId = request('layout_id');
        if ($layoutId !== null && $layoutId !== '') {
            $query->where('layout_id', (int) $layoutId);
        }
        $screens = $query->get();
        
        // Get layout information if layout_id is provided
        $layoutInfo = null;
        if ($layoutId !== null && $layoutId !== '') {
            $layout = DeviceLayout::find($layoutId);
            if ($layout) {
                $layoutInfo = [
                    'id' => $layout->id,
                    'name' => $layout->layout_name,
                    'type' => $layout->layout_type,
                    'type_name' => $layout->layout_type_name,
                    'max_screens' => $layout->max_screens,
                    'current_screens' => $screens->count(),
                    'remaining_slots' => $layout->remaining_screen_slots,
                    'can_add_more' => $layout->canAddMoreScreens()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'screens' => $screens,
            'layout_info' => $layoutInfo,
            'counts' => [
                'total' => $device->screens_count,
            ]
        ]);
    }
}
