<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ForgetPasswordNotification;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class Supervisor extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, HasFactory, HasRoles;
    protected $guarded = [];
    protected $guard_name = 'supervisor';
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ForgetPasswordNotification($token, 'supervisor'));
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifySupervisorEmailNotification);
    }
}