<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportPromocode extends \Eloquent
{
    use HasFactory;

    protected $fillable = [
        'trade_network_id',
        'product_id',
        'filename',
    ];
}
