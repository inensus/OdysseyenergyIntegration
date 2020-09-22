<?php


namespace Inensus\OdysseyS3Integration\Commands;


use Illuminate\Console\Command;

class InstallS3IntegrationPackage extends Command
{

    protected $signature = 'odyssey-s3:install';
    protected $description = 'Install the Odyssey S3 Integration Package';

    public function handle(): void
    {
        $this->info('Installing Odyssey S3 Integration Package\n');

        $this->info('Copying config file');
        $this->call('vendor:publish', [
            '--provider' => "Inensus\OdysseyS3Integration\OdysseyS3IntegrationServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('Copying migrations\n');
        $this->call('vendor:publish', [
            '--provider' => "Inensus\OdysseyS3Integration\OdysseyS3IntegrationServiceProvider",
            '--tag' => "migrations"
        ]);

        $this->info('Creating database tables\n');
        $this->call('migrate', [
            '--path' => 'migrations',

        ]);


        $this->info('Package installed successfully..');
    }
}
