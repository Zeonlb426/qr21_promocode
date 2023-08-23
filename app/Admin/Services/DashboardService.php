<?php

declare(strict_types=1);

namespace App\Admin\Services;

use App\Models\TradeNetwork;
use Illuminate\Support\Collection;

/**
 * Class DashboardService
 * @package App\Admin\Services
 */
final class DashboardService
{
    /**
     * @return int
     */
    public function tradeNetworkCount(): int
    {
        return TradeNetwork::where('status',true)->count();
    }

    /**
     * @return \App\Models\TradeNetwork[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function tradeNetworks()
    {
        return TradeNetwork::where('status',true)
            ->with(['typePromocode'])
            ->select(['id', 'name', 'type_promocode_id', 'send_status'])
            ->get()
            ->keyBy('id')
        ;
    }

    public function promocodes(): Collection
    {
        return \DB::table('promocodes')
            ->selectRaw(
                "COUNT(*) AS total,".
                "SUM(CASE WHEN free = true THEN 1 ELSE 0 END) AS free,".
                "trade_network_id"
            )
            ->groupBy('trade_network_id')
            ->orderBy('free')
            ->get()
            ->keyBy('trade_network_id')
        ;
    }
}
