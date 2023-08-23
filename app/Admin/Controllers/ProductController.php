<?php

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\Product;

class ProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Товары';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new Product());

        $grid->sortable();

        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableBatchActions();

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->disableFilter();

        $grid->column('sort', __('Сортировка'))->sortable();
        $grid->column('image', __('Изображение'))
            ->display(fn() => \optional(\optional($this)->imageMedia)->getFullUrl())
            ->image('', 100, 80)
        ;
        $grid->column('name', __('Имя устройства'));

        $grid->column('status', __('Включено'))
            ->switch([
                'on'  => ['value' => 1, 'text' => 'Вкл', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;

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
    protected function form()
    {
        $form = new Form(new Product());

        $form->setView('admin.product.form');

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->text('name', __('Имя устройства'))
            ->rules('required|max:100', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна в 100 символов'),
            ])
        ;
        $form->text('short_name', __('Обозначение для Mindbox'))
            ->rules('required|max:50', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна в 50 символов'),
            ])
        ;
        $form->switch('status', __('Статус'))->default(1)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Вкл', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;
        $form->text('title', __('Заголовок'))
            ->rules('required|max:255', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна в 255 символов'),
            ])
        ;
        $form->text('label', __('Примечание'));
        $form->text('sub_title', __('Подзаголовок'));
        $form->mediaLibrary(Product::MEDIA_COLLECTION_IMAGE, __('Изображение'))->required();
        $form->number('sort', __('Сортировка'))->min(0)->default(0);

        return $form;
    }
}
