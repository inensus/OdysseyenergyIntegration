<?php

namespace Inensus\OdysseyS3Integration\Providers;

use Illuminate\Support\ServiceProvider;
use Inensus\OdysseyS3Integration\Commands\InstallS3IntegrationPackage;
use Inensus\OdysseyS3Integration\Commands\SyncTransactions;


class OdysseyS3IntegrationServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->app->register(OdysseyS3IntegrationRouteServiceProvider::class);
        if ($this->app->runningInConsole()) {
            $this->publishConfigFiles();
            $this->publishVueFiles();
            $this->publishMigrations();
            $this->commands([InstallS3IntegrationPackage::class, SyncTransactions::class]);

        }
    }

    public function publishMigrations()
    {
        if (!class_exists('CreateSmGrids')) {
            $timestamp = date('Y_m_d_His');
            $this->publishes([
                __DIR__ . '/../../database/migrations/create_odyssey_s3_credentials.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_s3_credentials.php",
                __DIR__ . '/../../database/migrations/create_odyssey_s3_sync_objects.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_s3_sync_objects.php",
                __DIR__ . '/../../database/migrations/create_odyssey_s3_sync_histories.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_s3_sync_histories.php",
                __DIR__ . '/../../database/migrations/create_odyssey_s3_sync_object_tags.php.stub' => $this->app->databasePath() . "/migrations/{$timestamp}_create_s3_sync_object_tags.php",
            ], 'migrations');
        }
    }
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/odyssey-s3-integration.php',
            'odysseyS3Integration'
        );
    }
    public function publishConfigFiles()
    {
        $this->publishes([
            __DIR__ . '/../../config/odyssey-s3-integration.php' => config_path('odyssey-s3-integration.php'),
        ]);
    }


    public function publishVueFiles()
    {
        $this->publishes([
            __DIR__ . '/../resources/assets' => resource_path('assets/js/plugins/odyssey-s3'
            ),
        ], 'vue-components');
    }
}
