<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory ,SoftDeletes;
    protected $guarded = [];
    public function experiments()
    {
        return $this->belongsToMany(Experiment::class, 'device_experiment', 'device_id', 'experiment_id');
    }
}
