<?php


namespace Inensus\OdysseyS3Integration\Providers;


use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class OdysseyS3IntegrationRouteServiceProvider  extends ServiceProvider
{
    protected $namespace = 'Inensus\OdysseyS3Integration\Http\Controllers';


   public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__.'/../routes/web.php');
    }
}
