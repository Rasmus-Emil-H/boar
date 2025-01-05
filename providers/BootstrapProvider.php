<?php

namespace app\providers;

use \app\core\src\providers\ServiceProvider;
use \app\services\WeatherAPI;

class BootstrapProvider implements ServiceProvider {

    public function register(): void {
        app()->getServiceProvider()->bind(WeatherAPI::class);
    }

    public function boot(): void {
        
    }

}