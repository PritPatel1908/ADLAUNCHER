<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceScreen extends Model
{
    use HasFactory;

    protected $fillable = [
        'screen_no',
        'screen_height',
        'screen_width',
        'device_id',
        'layout_id',
    ];

    protected $casts = [
        'screen_no' => 'integer',
        'screen_height' => 'integer',
        'screen_width' => 'integer',
        'device_id' => 'integer',
        'layout_id' => 'integer',
    ];

    /**
     * Parent device relation.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Layout relation.
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(DeviceLayout::class, 'layout_id');
    }
}
