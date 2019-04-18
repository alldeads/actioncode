<?php

namespace Encore\Action;

use Encore\Admin\Extension;

class Action extends Extension
{
    public $name = 'action';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Action',
        'path'  => 'action',
        'icon'  => 'fa-gears',
    ];
}