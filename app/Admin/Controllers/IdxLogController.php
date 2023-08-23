<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\TradeNetwork;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use \App\Models\IdxLog;

class IdxLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Журнал регистрации запросов в IDX';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new IdxLog());

        $grid->model()->orderByDesc('created_at');

        $grid->disableColumnSelector();
        $grid->disableActions();
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->paginate(50);

        $tradeNetwork = TradeNetwork::get(['id', 'name']);
        $listTradeNetwork = $tradeNetwork->mapWithKeys(function ($item, $key) {
            return [$item['name'] => $item['name']];
        });
        $product = Product::get(['id', 'name']);
        $listProduct = $product->mapWithKeys(function ($item, $key) {
            return [$item['name'] => $item['name']];
        });
        $grid->filter(static function (Grid\Filter $filter) use ($listTradeNetwork, $listProduct) {
            $filter->disableIdFilter();
            $filter->like('phone', __('Телефон'));
            $filter->like('trade_network', __('Торговая сеть'))->select($listTradeNetwork->all());
            $filter->like('product', __('Вариация продукта'))->select($listProduct->all());
        });

        $grid->column('method', __('Метод'));
        $grid->column('phone', __('Телефон'));
        $grid->column('result_code_text', __('Результат выполнения'));
        $grid->column('score_text', __('Текстовое описание результата'));
        $grid->column('trade_network', __('Торговая сеть'));
        $grid->column('url', __('Домен'));
        $grid->column('product', __('Продукт'));
        $grid->column('duration', __('Время выполнения в сек.'));
        $grid->column('created_at', __('Дата'))->sortable();

//        Admin::style('
//            li:nth-child(3) {	display: none !important; }
//        ');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     */
    protected function detail($id)
    {
        Permission::error();
    }

    /**
     * Make a form builder.
     */
    protected function form()
    {
        Permission::error();
    }
}
