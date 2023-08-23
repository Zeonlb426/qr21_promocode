<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionCustomer extends \Eloquent
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_network_id',
        'code',
        'salt',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
