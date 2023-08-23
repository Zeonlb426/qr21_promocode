<?php

declare(strict_types=1);

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promocode extends \Eloquent
{
    use HasFactory, DefaultDatetimeFormat;

    protected $fillable = [
        'code',
        'trade_network_id',
        'product_id',
        'free',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tradeNetwork(): BelongsTo
    {
        return $this->belongsTo(TradeNetwork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
