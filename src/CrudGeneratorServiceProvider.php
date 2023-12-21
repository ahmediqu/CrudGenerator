<?php

namespace Crud\CrudGenerator;

use Crud\CrudGenerator\Commands\CrudGeneratorCommand;
use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudGeneratorCommand::class,
            ]);
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/crudgenerator.php', 'crudgenerator');

        // Register the service the package provides.
        $this->app->singleton('crudgenerator', function ($app) {
            return new CrudGenerator;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['crudgenerator'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/crudgenerator.php' => config_path('crudgenerator.php'),
        ], 'crudgenerator');
    }
}
