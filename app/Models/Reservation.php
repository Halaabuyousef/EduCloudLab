<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'prestart_notified_at' => 'datetime',
        'notified_started_at' => 'datetime',
        'notified_completed_at' => 'datetime',
    ];

    // نخليها تظهر تلقائياً كخاصية إضافية
    protected $appends = ['runtime_status'];

    public function getRuntimeStatusAttribute(): string
    {
        $now = now();

        // حالات يدوية نهائية لا نتجاوزها
        if (in_array($this->status, ['cancelled', 'postponed'])) {
            return $this->status;
        }

        // وقتياً: Active خلال النافذة
        if ($this->start_time && $this->end_time && $now->between($this->start_time, $this->end_time)) {
            return 'active';
        }

        // منتهي: Completed إذا خلص الوقت
        if ($this->end_time && $now->gte($this->end_time)) {
            return 'completed';
        }

        // قبل البداية: Pending
        if ($this->start_time && $now->lt($this->start_time)) {
            return 'pending';
        }

        // fallback
        return $this->status ?? 'pending';
    }

    /* ===== Scopes مبنية على الوقت ===== */

    // نشِط الآن (ما عدا الملغي/المؤجّل)
    public function scopeTimeActive($q)
    {
        return $q->where('start_time', '<=', now())
            ->where('end_time', '>',  now())
            ->whereNotIn('status', ['cancelled', 'postponed']);
    }

    // محجوز للمستقبل (لم يبدأ بعد) وغير ملغي/مؤجّل
    public function scopeTimePending($q)
    {
        return $q->where('start_time', '>', now())
            ->whereNotIn('status', ['cancelled', 'postponed']);
    }

    // منتهي أو ملغي
    public function scopeTimeFinished($q)
    {
        return $q->where('end_time', '<=', now())
            ->orWhereIn('status', ['completed', 'cancelled']);
    }
/////////////////////////////////////
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
