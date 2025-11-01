<?php

namespace RobinNcode\LaravelDbCraft;

use Illuminate\Support\ServiceProvider;
use RobinNcode\LaravelDbCraft\Commands\GenerateMigrationCommand;
use RobinNcode\LaravelDbCraft\Commands\GenerateSeederCommand;

class DbCraftServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/db-craft.php', 'db-craft'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/db-craft.php' => config_path('db-craft.php'),
            ], 'db-craft-config');

            $this->commands([
                GenerateMigrationCommand::class,
                GenerateSeederCommand::class,
            ]);
        }
    }
}