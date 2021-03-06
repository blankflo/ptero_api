<?php

namespace Ptero\Request;
use Server;
use Application;

use Illuminate\Support\ServiceProvider;

class PteroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('servers', function ($app) {
            return new Server();
          });
          $this->app->bind('application', function ($app) {
            return new Application();
          });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
