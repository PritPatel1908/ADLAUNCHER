<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_name',
        'schedule_start_date_time',
        'schedule_end_date_time',
        'device_id',
        'layout_id',
        'screen_id',
    ];

    protected $casts = [
        'schedule_start_date_time' => 'datetime',
        'schedule_end_date_time' => 'datetime',
        'device_id' => 'integer',
        'layout_id' => 'integer',
        'screen_id' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(DeviceLayout::class, 'layout_id');
    }

    public function screen(): BelongsTo
    {
        return $this->belongsTo(DeviceScreen::class, 'screen_id');
    }

    public function medias(): HasMany
    {
        return $this->hasMany(ScheduleMedia::class);
    }
}
