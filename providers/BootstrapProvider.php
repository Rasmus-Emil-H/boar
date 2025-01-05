<?php

namespace app\providers;

use \app\core\Application;
use \app\core\src\providers\ServiceProvider;
use \app\services\WeatherAPI;

class BootstrapProvider implements ServiceProvider {

    public function register(): void {
        app()->getServiceProvider()->bind(WeatherAPI::class, function(Application $app) {
            var_dump($app);
            // return $app->setGlobalService(new WeatherAPI());
        });
    }

}