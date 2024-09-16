<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jenssegers\Mongodb\Connection as MongoConnection;

class MongoDBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('mongodb', function ($app) {
            return new MongoConnection(
                $app['config']['database.connections.mongodb'],
                $app['config']['database.connections.mongodb.options']
            );
        });
    }
}
