<?php

namespace Inensus\OdysseyS3Integration;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inensus\OdysseyS3Integration\Commands\InstallS3IntegrationPackage;
use Inensus\OdysseyS3Integration\Commands\SyncTransactions;


class OdysseyS3IntegrationServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->registerRoutes();
        if ($this->app->runningInConsole()) {
            $this->commands([InstallS3IntegrationPackage::class, SyncTransactions::class]);
            $this->publishVueFiles();
        }

        /*$this->publishes([
            __DIR__ . '/../config/odysseyS3Integration.php' => config_path('odysseyS3Integration.php'),
        ], 'config');*/

        if (!class_exists('CreateS3Credentials')) {
            $timestamp = date('Y_m_d_His');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_s3_credentials.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_s3_credentials.php",
                __DIR__ . '/../database/migrations/create_sync_objects.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_sync_objects.php",
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/odyssey-s3-integration.php',
            'odysseyS3Integration'
        );
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });

    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('s3package.prefix'),
            'middleware' => config('s3package.middleware'),
        ];
    }

    public function publishVueFiles()
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/js/components' => public_path('s3Integration'),
        ], 'vue');
    }

}
