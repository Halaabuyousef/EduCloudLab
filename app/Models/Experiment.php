<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experiment extends Model
{
    use HasFactory , SoftDeletes;
    public const STATUS_AVAILABLE   = 'available';
    public const STATUS_RESERVED    = 'reserved';
    public const STATUS_IN_USE      = 'in_use';
    public const STATUS_MAINTENANCE = 'maintenance';
        protected $guarded = [];


    public function activeReservationsNow()
    {
        return $this->reservations()
            ->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time',   '>',  now());
    }

    public function upcomingReservations()
    {
        return $this->reservations()
            ->where('status', 'active')
            ->where('start_time', '>', now());
    }

    /** تحديث الحالة على أساس الحجوزات */
    public function refreshStatus(): void
    {
        // لا تغيّر حالة الصيانة
        if ($this->status === 'maintenance') return;
        $now = Carbon::now();
        $hasActive = $this->reservations()
            ->whereIn('status', ['active', 'approved', 'pending'])
            ->where('start_time', '<=', $now)
            ->where('end_time', '>', $now)
            ->exists();

        if ($hasActive) {
            $this->update(['status' => 'in_use']);
            return;
        }

        // حجوزات قادمة
        $hasUpcoming = $this->reservations()
            ->whereIn('status', ['approved', 'pending'])
            ->where('start_time', '>', $now)
            ->exists();

        $this->update(['status' => $hasUpcoming ? 'reserved' : 'available']);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
    public function devices()
    {
        return $this->belongsToMany(Device::class, 'device_experiment', 'experiment_id', 'device_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function sensors()
    {
        return $this->belongsToMany(Sensor::class, 'experiment_sensor');
    }
}
