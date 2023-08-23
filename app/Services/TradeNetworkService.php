<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TradeNetwork;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class
 *
 * @package App\Services
 */
final class TradeNetworkService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return TradeNetwork::where('status', '=', true)
            ->has('products')
            ->with(['typePromocode', 'products'])
            ->orderBy('sort')
            ->get();
    }
}
