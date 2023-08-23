<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => \config('admin.route.prefix'),
    'middleware' => \config('admin.route.middleware'),
    'as' => \config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', [\App\Admin\Controllers\HomeController::class, 'index'])->name('home');
    $router->resource('trade', \App\Admin\Controllers\TradeNetworkController::class);
    $router->resource('promocodes', \App\Admin\Controllers\PromocodeController::class)->names('promocodes');
    $router->resource('promocode-logs', \App\Admin\Controllers\PromocodeLogController::class);
    $router->resource('idx-logs', \App\Admin\Controllers\IdxLogController::class);
    $router->resource('products', \App\Admin\Controllers\ProductController::class);
    $router->resource('mails', \App\Admin\Controllers\MailController::class);
    $router->resource('api-users', \App\Admin\Controllers\ApiUsersController::class);

});
