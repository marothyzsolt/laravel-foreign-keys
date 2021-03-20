<?php

namespace MarothyZsolt\LaravelForeignKeys;

use MarothyZsolt\LaravelForeignKeys\Http\Middleware\RedirectMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelForeignKeysServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/foreign_keys.php' => config_path('foreign-keys.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/foreign_keys.php', 'foreign-keys');
    }
}
