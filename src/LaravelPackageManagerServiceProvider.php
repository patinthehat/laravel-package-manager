<?php

namespace LaravelPackageManager;

use Illuminate\Support\ServiceProvider;
use LaravelPackageManager\Console\Commands\RequirePackageCommand;

class LaravelPackageManagerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
          \LaravelPackageManager\Console\Commands\RequirePackageCommand::class,
          \LaravelPackageManager\Console\Commands\UnregisterPackageCommand::class,
        ]);
    }

}
