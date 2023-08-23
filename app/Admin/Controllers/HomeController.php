<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Components\InfoBox;
use App\Admin\Services\DashboardService;
use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Collection;


/**
 * Class HomeController
 * @package App\Admin\Controllers
 */
final class HomeController extends Controller
{
    private DashboardService $service;

    /**
     * @param \App\Admin\Services\DashboardService $service
     */
    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * @param \Encore\Admin\Layout\Content $content
     *
     * @return \Encore\Admin\Layout\Content
     */
    public function index(Content $content): Content
    {
        return $content
            ->title('Статистика')
            ->description('информация о торговых сетях')
            ->row(function (Row $row) {
                $row->column(12, <<<HTML
                    <div style="display: flex;margin-bottom: 30px;">
                        <div style="display: grid;grid-template-columns: 22px 1fr;gap: 5px;">
                            <span class="label label-success"> </span>
                            <span class="text"> - Больше 1000 шт.</span>
                        </div>
                        
                        <div style="margin-left: 20px;display: grid;grid-template-columns: 22px 1fr;gap: 5px;">
                            <span class="label label-warning"> </span>
                            <span class="text"> - От 300 до 1000 шт.</span>
                        </div>
                        
                        <div style="margin-left: 20px;display: grid;grid-template-columns: 22px 1fr;gap: 5px;">
                            <span class="label label-danger"> </span>
                            <span class="text"> - От 0 до 300 шт.</span>
                        </div>
                    </div>
                    HTML
                );
            })
            ->row(function (Row $row) {
                $tradeNetworks = $this->service->tradeNetworks();
                $collectionPromocodesCount = $this->service->promocodes();
                $data = new Collection();
                foreach ($tradeNetworks as $tradeNetwork) {
                    $total = 0;
                    $free = 0;
                    $busy = 0;
                    if (isset($collectionPromocodesCount[$tradeNetwork->id])) {
                        $total = $collectionPromocodesCount[$tradeNetwork->id]->total;
                        $free = $collectionPromocodesCount[$tradeNetwork->id]->free;
                        $busy = $collectionPromocodesCount[$tradeNetwork->id]->total - $collectionPromocodesCount[$tradeNetwork->id]->free;
                    }
                    $data->push([
                        'title' => $tradeNetwork->name,
                        'total' => $total,
                        'free' => $free,
                        'busy' => $busy,
                        'type_promocode' => $tradeNetwork->typePromocode->short_name,
                        'send' => $tradeNetwork->send_status,
                    ]);
                }
                // Если необходимо выводить в порядке возрастания по полю "free"
                $sortData = $data->sortBy('free');
                foreach ($sortData as $item) {
                    $row->column(4, function (Column $column) use ($item) {
                        $infoBox = new InfoBox($item, 'custom');
                        $column->append($infoBox->render());
                    });
                }
                Admin::style('
                    .bg-custom {
                        margin-bottom: 20px;
                        color: #3e3e3e;
                        background: linear-gradient(121deg, rgb(255 255 255), rgb(243 243 255));
                        box-shadow: 0px 2px 4px 0px rgb(35 30 88 / 30%);
                    }
                    .bg-custom:hover {
                        box-shadow: 0px 5px 8px 0px rgb(39 35 86 / 17%);
                    }
                    .small-box:hover {
                        color: #000000 !important;
                    }
                    .small-box p {
                        font-size:14px !important;
                    }
                ');
            })
        ;
    }
}
