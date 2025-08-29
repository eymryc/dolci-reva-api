<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeSlot extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'start_time',
        'end_time',
        'max_participants'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    /**
     * Relation avec l'activité
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Vérifie si le créneau est complet
     */
    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_participants;
    }

    /**
     * Formate la plage horaire
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Vérifie si le créneau est à venir
     */
    public function isUpcoming(): bool
    {
        return $this->start_time > now();
    }
}
