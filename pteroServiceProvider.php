<?php

namespace Ptero_request\Manage_ptero;
use Server;
use Application;

use Illuminate\Support\ServiceProvider;

class pteroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('server', function ($app) {
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
