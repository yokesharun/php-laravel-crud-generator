<?php

namespace Yokesharun\Laravelcrud;

use Illuminate\Support\ServiceProvider;

class CRUDServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $viewPath = __DIR__.'/Views';
        $this->loadViewsFrom($viewPath, 'laravelcrud');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Yokesharun\Laravelcrud\Controllers\CRUDController');
    }
}
