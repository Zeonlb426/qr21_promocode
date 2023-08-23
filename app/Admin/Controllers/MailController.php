<?php

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use \App\Models\Mail;

class MailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Адреса электронной почты';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid(new Mail());

        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableBatchActions();

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->disableFilter();

        $grid->column('mail', __('Адрес почты'));
        $grid->column('status', __('Участвует в рассылке'))
            ->switch([
                'on'  => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Нет'],
            ])
        ;
        $grid->column('description', __('Описание'));
        $grid->column('created_at', __('Создано'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id): Show
    {
        Permission::error();
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        $form = new Form(new Mail());

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->email('mail', __('Адрес почты'))
            ->rules('required|email|max:60', [
                'required' => __('Поле обязательно для заполнения'),
                'email' =>  __('Некорректный email'),
                'max' => __('Превышена максимальная длинна в 60 символов'),
            ])
        ;
        $form->switch('status', __('Участвует в рассылке'))->default(1)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Вкл', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;
        $form->textarea('description', __('Описание'))->help('Поле для заметок. Необязательно при заполнении.');

        return $form;
    }
}
