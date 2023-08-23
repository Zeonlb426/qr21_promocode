<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\Product;
Use Encore\Admin\Admin;
use App\Models\TypePromocode;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use \App\Models\TradeNetwork;

class TradeNetworkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Торговые сети';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TradeNetwork());

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
        $grid->column('name', __('Название сети'))->display(function ($name) {
            $href = \URL::current().'/'.$this->id.'/edit';
            $template = '
                    <div style="display: grid; justify-content: start;">
                        <a href=%s><span class="network">%s</span></a>
                    </div>
                ';
            return sprintf($template, $href, $name);
        });
        $grid->column('status', __('Активна'))
            ->switch([
                'on'  => ['value' => 1, 'text' => 'Вкл'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;
        $grid->column('url', __('Домен сети'));
        $grid->column('type_promocode_id', __('Тип промокода'))
            ->display(function () {
                return $this->typePromocode->name;
            })
        ;
        $grid->column('send_status', __('Рассылка оповещения'))
            ->switch([
                'on'  => ['value' => 1, 'text' => 'Вкл'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;

        Admin::style('
            .bootstrap-switch .bootstrap-switch-handle-on.bootstrap-switch-primary {background: #33b77a !important;}
            .network {padding: 4px 8px;font-size: 14px;font-weight: 600;color: #000;background-color: #e8e7fd;border-radius: 6px;}
        ');

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
        $form = new Form(new TradeNetwork());

        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->switch('status', __('Статус'))->default(1)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Вкл', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
        ;
        $form->text('name', __('Название торговой сети'))
            ->rules('required|max:255', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна'),
            ])
        ;
        $form->url('url', __('Домен торговой сети'))
            ->rules('required|max:255', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна'),
            ])
        ;
        $form->select('type_promocode_id', __('Тип промокода'))
            ->options(TypePromocode::all()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;

        $form->switch('send_status', __('Рассылка'))->default(1)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Вкл', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Откл'],
            ])
            ->help('Рассылка оповещения на электронную почту об окончании свободных промокодов')
        ;

        $form->checkbox('products', __('Вариация продукта'))
            ->options(Product::where('status', true)->get()->pluck('name', 'id'))
            ->rules('required', ['required' => __('Поле обязательно для заполнения')])
        ;
        $form->embeds('order','Порядок сортировки продукта при показе', function ($form) {
            $products = Product::where('status', true)->get()->pluck('name', 'id');
            foreach ($products as $key => $value) {
                $form->number($key, $value)->min(0)->max(9)->default(0);
            }
        });
        $form->text('title', __('Заголовок'))
            ->rules('required|max:255', [
                'required' => __('Поле обязательно для заполнения'),
                'max' => __('Превышена максимальная длинна'),
            ])
        ;
        $form->text('sub_title', __('Подзаголовок'))
            ->rules('max:255', [
                'max' => __('Превышена максимальная длинна'),
            ])
        ;
        $form->divider();
        $form->switch('show_instruction', __('Отображать инструкцию для кассира'))->default(0)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Нет'],
            ])
        ;
        $form->text('instruction_title', __('Заголовок инструкции для кассира'))
            ->rules('required_if:show_instruction,on', ['required_if' => __('Поле обязательно для заполнения')])
        ;
        $form->list('instruction_questions', 'Пункт инструкции');
        $form->divider();
        $form->switch('quiz_show', __('Отображать опрос'))->default(0)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Нет'],
            ])
        ;
        $form->text('quiz_question', __('Вопрос'))
            ->rules('required_if:quiz_show,on', ['required_if' => __('Поле обязательно для заполнения')])
        ;
        $form->list('quiz_answers', 'Варианты ответов')
            ->rules('required_if:quiz_show,on', ['required_if' => __('Поле обязательно для заполнения')])
        ;
        $form->select('quiz_type_answers', __('Возможность выбрать'))->default('radio')
            ->options(['radio'=>'Только один вариант', 'checkbox'=>'Несколько вариантов'])
            ->rules('required_if:quiz_show,on', ['required_if' => __('Поле обязательно для заполнения')])
        ;
        $form->switch('quiz_own_answer', __('Наличие собственного варианта ответа'))->default(0)
            ->states([
                'on'  => ['value' => 1, 'text' => 'Да', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Нет'],
            ])
        ;
        $form->number('sort', __('Сортировка'))->min(0)->default(0);

        Admin::style('
            .bootstrap-switch .bootstrap-switch-handle-on.bootstrap-switch-success {background: #33b77a !important;}
            .select2-selection--multiple .select2-selection__choice {background-color: #766bf1 !important;border-color: #9990f5 !important;padding: 1px 10px;color: #fff;}
        ');

        return $form;
    }
}
