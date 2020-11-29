<?php


namespace Inensus\OdysseyS3Integration\Commands;


use Illuminate\Console\Command;
use Inensus\OdysseyS3Integration\Models\OdysseyS3SyncObjectTag;
use Inensus\OdysseyS3Integration\Services\MenuItemService;
use Inensus\OdysseyS3Integration\Services\S3AuthorizationService;

class InstallS3IntegrationPackage extends Command
{

    protected $signature = 'odyssey-s3:install';
    protected $description = 'Install the Odyssey S3 Integration Package';

    private $s3AuthorizationService;
    private $menuItemService;
    private $odysseyS3SyncObjectTag;
    public function __construct(
        MenuItemService $menuItemService,
        S3AuthorizationService $s3AuthorizationService,
        OdysseyS3SyncObjectTag $odysseyS3SyncObjectTag
    )
    {
        parent::__construct();
        $this->menuItemService = $menuItemService;
        $this->s3AuthorizationService=$s3AuthorizationService;
        $this->odysseyS3SyncObjectTag = $odysseyS3SyncObjectTag;
    }

    public function handle(): void
    {
        $this->info('Installing Odyssey S3 Integration Package\n');

       $this->info('Copying migrations\n');
        $this->call('vendor:publish', [
            '--provider' => "Inensus\OdysseyS3Integration\Providers\OdysseyS3IntegrationServiceProvider",
            '--tag' => "migrations"
        ]);

        $this->info('Creating database tables\n');
        $this->call('migrate', [
            '--path' => 'migrations',

        ]);

        $this->call('plugin:add', [
            'name' => "Odyssey-S3",
            'composer_name' => "inensus/odyssey-s3-integration",
            'description' => "Enables to push data to OdysseyEnergy via AmazonS3",
        ]);
        $this->call('routes:generate');

        $menuItems = $this->menuItemService->createMenuItems();
        $this->call('menu-items:generate', [
            'menuItem' => $menuItems['menuItem'],
            'subMenuItems' => $menuItems['subMenuItems'],
        ]);

        $this->odysseyS3SyncObjectTag->newQuery()->create([
            'name'=>'Transaction'
        ]);
        $this->info('Transaction tag created..');

        $this->info('Copying vue files\n');

        $this->call('vendor:publish', [
            '--provider' => "Inensus\OdysseyS3Integration\Providers\OdysseyS3IntegrationServiceProvider",
            '--tag' => "vue-components"
        ]);

        $this->call('sidebar:generate');

        $this->info('Package installed successfully..');
    }
}
