<?php

namespace App\Models;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experiment extends Model
{
    use HasFactory , SoftDeletes;
    protected $guarded = [];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
    public function device()
    {
        return $this->belongsTo(Device::class);
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
