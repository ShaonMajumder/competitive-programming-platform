<?php

namespace App\Modules\Submissions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SubmissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'submission');
        $this->registerWebRoutes();
        $this->registerApiRoutes();
    }

    /**
     * Register web routes for the Submissions module.
     */
    protected function registerWebRoutes()
    {
        Route::middleware('web')
            ->namespace('App\Modules\Submissions\Controllers')
            ->group(__DIR__ . '/Routes/web.php');
    }

    /**
     * Register API routes for the Submissions module.
     */
    protected function registerApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace('App\Modules\Submissions\Controllers')
            ->group(__DIR__ . '/Routes/api.php');
    }
}
