<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use \App\Models\ApiUser;

class ApiUsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Пользователи API для сторонних сервисов';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid(new ApiUser());

        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableBatchActions();

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->disableFilter();

        $grid->column('login', __('Логин'));
        $grid->column('password', __('Пароль'));
        $grid->column('created_at', __('Дата создания'));

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
    protected function form(): Form
    {
        $form = new Form(new ApiUser());

        $form->setView('admin.api.form');

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->text('login', __('Логин'))
            ->creationRules(['required','max:16', "unique:api_users"])
            ->updateRules(['required', "unique:api_users,login,{{id}}"])
        ;
        $form->text('password', __('Пароль'))->rules('required|min:10');

        return $form;
    }

    /**
     * @param \Encore\Admin\Layout\Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function index(Content $content): Content
    {
        $content->row(function (Row $row) {
            $row->column(12, <<<HTML
                    <div style="display: grid;margin-bottom: 30px;">
                        <h4>Действия для подключения к API</h4>
                        <span class="txt">1 - Создать пользователя.</span>
                        <span class="txt">2 - Передать стороннему сервису "Логин и Пароль".</span>
                        <span class="txt">3 - Сервис осуществляет POST запрос на адрес /api/token. В теле запроса необходимо указать JSON {"login": "Логин", "password": "Пароль"}.<br> В ответ будет выдан токен для подключения к API {"access_token": "Токен"}.</span>
                        <span class="txt">4 - При обращении к API, в заголовке запроса указать Authorization Bearer полученный токен.</span>
                    </div>
                    HTML
            );
        });
        return parent::index($content);
    }
}
