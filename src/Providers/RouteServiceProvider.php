<?php

namespace Dannerz\Api\Providers;

use Dannerz\Api\Controllers\AuthController;
use Dannerz\Api\Controllers\DefaultController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        $this->mapAuthRoutes();

        $this->mapDefaultRoute();
    }

    protected function mapAuthRoutes()
    {
        Route::prefix('auth')
            ->middleware('auth')
            ->group(function () {
                Route::post('auth/login', '\\'.AuthController::class.'@login');
                Route::post('auth/logout', '\\'.AuthController::class.'@logout');
                Route::post('auth/user', '\\'.AuthController::class.'@user');
            });
    }

    protected function mapDefaultRoute()
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(function () {
                Route::any('/{any}', '\\'.DefaultController::class.'@handle')->where('any', '.*');
            });
    }
}
