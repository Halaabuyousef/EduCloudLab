<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes ,HasRoles, Notifiable;
    protected $guarded = [];
    protected $guard_name = 'admin';
}
