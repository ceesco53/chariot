<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CloudFoundryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // If the app is running in a PaaS environment.
        if (env('VCAP_SERVICES', null) !== null) {
            // Decode the JSON provided by Cloud Foundry.
            $config = json_decode(env('VCAP_SERVICES'), true);
            $mysqlConfig = $config['cleardb'][0]['credentials'];
            $redisConfig = $config['rediscloud'][0]['credentials'];

            // Set the MySQL config.
            Config::set('database.connections.mysql.host', $mysqlConfig['hostname']);
            Config::set('database.connections.mysql.port', $mysqlConfig['port']);
            Config::set('database.connections.mysql.database', $mysqlConfig['name']);
            Config::set('database.connections.mysql.username', $mysqlConfig['username']);
            Config::set('database.connections.mysql.password', $mysqlConfig['password']);

            // Set the Redis config.
            Config::set('database.redis.clusters.default.0.host', $redisConfig['hostname']);
            Config::set('database.redis.clusters.default.0.password', $redisConfig['password']);
            Config::set('database.redis.clusters.default.0.port', $redisConfig['port']);
        }
    }
}
