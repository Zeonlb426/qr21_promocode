<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypePromocode extends \Eloquent
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'short_name',
        'status',
    ];
}
