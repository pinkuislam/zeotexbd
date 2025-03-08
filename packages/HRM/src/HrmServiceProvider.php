<?php


namespace Oshnisoft\HRM;

use Illuminate\Support\ServiceProvider;

class HrmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hrm.php', 'hrm');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/hrm.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'hrm');
        $this->publishes([
            __DIR__ . '/../config/hrm.php' => config_path('hrm.php'),
        ], 'hrm');
    }
}
