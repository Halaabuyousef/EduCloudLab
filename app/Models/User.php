<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'bio',
    
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyUserEmailNotification);
    }
    public function deviceTokens()
    {
        return $this->hasMany(\App\Models\DeviceToken::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function supervisor()
    {
        return $this->belongsTo(\App\Models\Supervisor::class);
    }
    public function scopeIndependent($q)
    {
        return $q->whereNull('supervisor_id');
    }
    public function scopeAttached($q)
    {
        return $q->whereNotNull('supervisor_id');
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
