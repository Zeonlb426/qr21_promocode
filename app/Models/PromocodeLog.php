<?php

declare(strict_types=1);

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromocodeLog extends \Eloquent
{
    use HasFactory, DefaultDatetimeFormat;

    protected $fillable = [
        'promocode',
        'trade_network',
        'user_id',
        'mindbox_id',
        'product_id',
        'type_promocode_id',
        'url',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typePromocode(): BelongsTo
    {
        return $this->belongsTo(TypePromocode::class, 'type_promocode_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
