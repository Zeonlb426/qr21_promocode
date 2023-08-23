<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\TradeNetwork;
use App\Models\TypePromocode;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use \App\Models\PromocodeLog;

class PromocodeLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Журнал выдачи промокодов';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid(new PromocodeLog());

        $grid->model()->orderByDesc('created_at');

        $grid->disableColumnSelector();
        $grid->disableActions();
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->paginate(50);

        $product = Product::get(['id', 'name']);
        $listProduct = $product->mapWithKeys(function ($item, $key) {
            return [$item['id'] => $item['name']];
        });
        $typePromocode = TypePromocode::get(['id', 'name']);
        $listTypePromocode = $typePromocode->mapWithKeys(function ($item, $key) {
            return [$item['id'] => $item['name']];
        });
        $tradeNetwork = TradeNetwork::get(['id', 'name']);
        $listTradeNetwork = $tradeNetwork->mapWithKeys(function ($item, $key) {
            return [$item['name'] => $item['name']];
        });
        $grid->filter(static function (Grid\Filter $filter) use ($listProduct, $listTypePromocode, $listTradeNetwork) {
            $filter->disableIdFilter();
            $filter->equal('promocode', __('Промокод'));
            $filter->like('trade_network', __('Торговая сеть'))->select($listTradeNetwork->all());
            $filter->equal('user_id', __('Внутренний ИД клиента'));
            $filter->equal('mindbox_id', __('ИД клиента в Mindbox'));
            $filter->equal('product_id', __('Вариация продукта'))->select($listProduct->all());
            $filter->equal('type_promocode_id', __('Тип промокода'))->select($listTypePromocode->all());
            $filter->startsWith('created_at', __('Дата выдачи'))->date();
        });

        $grid->column('promocode', __('Промокод'));
        $grid->column('trade_network', __('Торговая сеть'));
        $grid->column('user_id', __('ИД Клиента'));
        $grid->column('mindbox_id', __('ИД Клиента в Mindbox'));
        $grid->column('product_id', __('Вариация продукта'))
            ->display(function () {
                return $this->product->name;
            })
        ;
        $grid->column('type_promocode_id', __('Тип промокода'))
            ->display(function () {
                return $this->typePromocode->name;
            })
        ;
        $grid->column('url', __('Сайт'));
        $grid->column('created_at', __('Выдан'))->sortable();

//        Admin::style('
//            li:nth-child(3) {	display: none !important; }
//        ');

        return $grid;
    }

    /**
     * @param $id
     *
     */
    protected function detail($id)
    {
        Permission::error();
    }

    protected function form()
    {
        Permission::error();
    }
}
