<?php

namespace app\providers;

use \app\core\Application;
use \app\core\src\providers\ServiceProvider;
use \app\services\WeatherAPI;

class BootstrapProvider extends ServiceProvider {

    public function register() {
        app()->bind(WeatherAPI::class, function(Application $app) {

        });
    }

}