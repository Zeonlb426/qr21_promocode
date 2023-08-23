<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Imports\PromocodeImport;
use App\Models\ImportPromocode;
use App\Models\Product;
use App\Models\TradeNetwork;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use \App\Models\Promocode;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class PromocodeController
 *
 * @package App\Admin\Controllers
 */
class PromocodeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Промокоды';

    /**
     * @param \Encore\Admin\Layout\Content $content
     *
     * @return \Encore\Admin\Layout\Content
     */
    public function create(Content $content): Content
    {
        return $content
            ->title($this->title)
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($this->formCreate());
    }

    /**
     * @return bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        return $this->formCreate()->store();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid(new Promocode());

        $grid->model()->orderByDesc('created_at');

        $grid->disableColumnSelector();
        $grid->disableExport();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->paginate(50);

        $tradeNetwork = TradeNetwork::get(['id', 'name']);
        $listTradeNetwork = $tradeNetwork->mapWithKeys(function ($item, $key) {
            return [$item['id'] => $item['name']];
        });
        $grid->filter(static function (Grid\Filter $filter) use ($listTradeNetwork) {
            $filter->disableIdFilter();
            $filter->equal('code', __('Промокод'));
            $filter->equal('trade_network_id', __('Торговая сеть'))->select($listTradeNetwork->all());
            $filter->equal('free', __('Свободен'))->select([
                'no' => 'Нет',
                'yes' => 'Да',
            ]);
            $filter->equal('cancellation', __('Погашен'))->select([
                'no' => 'Нет',
                'yes' => 'Да',
            ]);
            $filter->startsWith('created_at', __('Дата загрузки'))->date();
        });

        $grid->column('code', __('Промокод'));
        $grid->column('trade_network_id', __('Торговая сеть'))
            ->display(function () {
                return $this->tradeNetwork->name;
            })
            ->sortable()
        ;
        $grid->column('product_id', __('Вариация продукта'))
            ->display(function () {
                return $this->product->name;
            })
            ->sortable()
        ;
        $grid->column('free', __('Свободен'))->bool()->sortable();
        $grid->column('cancellation', __('Погашен'))->bool()->sortable();
        $grid->column('created_at', __('Дата загрузки'))->sortable();

        return $grid;
    }

    /**
     * @param $id
     */
    protected function detail($id)
    {
        Permission::error();
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function formCreate(): Form
    {
        $form = new Form(new ImportPromocode());

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->select('trade_network_id', __('Название торговой сети'))
            ->options(TradeNetwork::all()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;
        $form->select('product_id', __('Вариация продукта'))
            ->options(Product::all()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;
        $form->file('filename', 'Файл *.xls/xlsx')
            ->rules('required|mimes:xls,xlsx',
                [
                    'required' => __('Поле обязательно для заполнения'),
                    'mimes' => __('Неверный тип файла'),
                ])
        ;

        $form->saved(function (Form $form) {

            $import = new PromocodeImport($form->model()->trade_network_id, $form->model()->product_id);

            try {
                Excel::import($import, \storage_path('app').'/'.$form->model()->filename);
            }catch (\Exception $exception) {
                Storage::delete($form->model()->filename);
                $message = new MessageBag([
                    'title'   => 'Произошла ошибка',
                    'message' => 'Проверьте корректность данных в файле и повторите попытку',
                ]);
                return redirect(route(admin_get_route('promocodes.index')))
                    ->with(['error' => $message]);
            }

            $message = new MessageBag([
                'title'   => 'Сохранено',
                'message' => 'Импорт промокодов завершен. Импортировано: '. $import->getRowCount() .' промокодов.',
            ]);

            Storage::delete($form->model()->filename);

            return redirect(route(admin_get_route('promocodes.index')))->with(['success' => $message]);
        });

        return $form;
    }

    /**
     * @return \Encore\Admin\Form
     */
    protected function form(): Form
    {
        $form = new Form(new Promocode());

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->text('code', __('Code'))
            ->rules('required|max:256', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна символов'),
            ])
        ;
        $form->select('trade_network_id', __('Название торговой сети'))
            ->options(TradeNetwork::all()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;
        $form->select('product_id', __('Вариация продукта'))
            ->options(Product::all()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;

        $form->switch('free', __('Свободен'))
            ->states([
                'on'  => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Нет'],
            ])
        ;

        return $form;
    }
}
