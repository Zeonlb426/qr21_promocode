<?php

namespace App\Admin\Components;

use Encore\Admin\Widgets\Widget;
use Illuminate\Contracts\Support\Renderable;

class InfoBox extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin.info-box';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param $item
     * @param $color
     */
    public function __construct($item, $color)
    {
        $this->data = $item;

        $this->class("small-box bg-$color");
    }

    /**
     * @return string
     */
    public function render()
    {
        $variables = array_merge($this->data, ['attributes' => $this->formatAttributes()]);

        return view($this->view, $variables)->render();
    }
}
