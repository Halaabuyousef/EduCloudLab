<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_POSTPONED = 'postponed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED  = 'canceled';

    // Scopes
    public function scopeScheduled($q)
    {
        return $q->where('status', self::STATUS_SCHEDULED);
    }
    public function scopePostponed($q)
    {
        return $q->where('status', self::STATUS_POSTPONED);
    }
    public function scopeFinished($q)
    {
        return $q->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELED]);
    }
}
