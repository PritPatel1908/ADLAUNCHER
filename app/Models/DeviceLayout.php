<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLayout extends Model
{
    protected $fillable = [
        'layout_name',
        'layout_type',
        'device_id',
        'status'
    ];

    protected $casts = [
        'layout_type' => 'integer',
        'status' => 'integer',
        'device_id' => 'integer'
    ];

    // Layout type constants
    const LAYOUT_TYPE_FULL_SCREEN = 0;
    const LAYOUT_TYPE_SPLIT_SCREEN = 1;
    const LAYOUT_TYPE_THREE_GRID_SCREEN = 2;
    const LAYOUT_TYPE_FOUR_GRID_SCREEN = 3;

    // Status constants
    const STATUS_DELETE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_BLOCK = 3;

    /**
     * Get the device that owns the layout.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get layout type name
     */
    public function getLayoutTypeNameAttribute(): string
    {
        return match ($this->layout_type) {
            self::LAYOUT_TYPE_FULL_SCREEN => 'Full Screen',
            self::LAYOUT_TYPE_SPLIT_SCREEN => 'Split Screen',
            self::LAYOUT_TYPE_THREE_GRID_SCREEN => 'Three Grid Screen',
            self::LAYOUT_TYPE_FOUR_GRID_SCREEN => 'Four Grid Screen',
            default => 'Unknown'
        };
    }

    /**
     * Get status name
     */
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DELETE => 'Delete',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_BLOCK => 'Block',
            default => 'Unknown'
        };
    }
}
