<?php

use Encore\Action\Http\Controllers\ActionController;

Route::get('action', ActionController::class.'@index');

Route::post('action/upload', ActionController::class.'@upload');