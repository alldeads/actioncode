<?php

namespace Encore\Action;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\Admin;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Action $extension)
    {
        if (! Action::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'action');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/action')],
                'action'
            );
        }

        Admin::booting(function () {
            Admin::js('vendor/laravel-admin-ext/action/datatables.js');
            Admin::js('vendor/laravel-admin-ext/action/action.js');
            Admin::css('vendor/laravel-admin-ext/action/datatables.min.css');
        });

        $this->app->booted(function () {
            Action::routes(__DIR__.'/../routes/web.php');
        });
    }
}