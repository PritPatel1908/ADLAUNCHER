<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleMedia extends Model
{
    use HasFactory;

    protected $table = 'schedule_medias';

    protected $fillable = [
        'media_file',
        'schedule_id',
        'media_type',
        'title',
    ];

    protected $casts = [
        'schedule_id' => 'integer',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
