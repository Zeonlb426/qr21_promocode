<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class ApiUser extends \Eloquent
{
    use HasApiTokens, HasFactory, Authenticatable, DefaultDatetimeFormat;

    protected $primaryKey = 'id';

    protected $fillable = [
        'login',
        'password',
    ];
}
