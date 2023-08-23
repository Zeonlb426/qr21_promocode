<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends \Eloquent
{
    use HasApiTokens, HasFactory, Authenticatable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'phone',
        'mindbox_id',
        'ban',
        'last_time_sent_code',
    ];
}
