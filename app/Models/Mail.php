<?php

declare(strict_types=1);

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mail extends \Eloquent
{
    use HasFactory, DefaultDatetimeFormat;

    protected $fillable = [
        'mail',
        'status',
        'description',
    ];
}
