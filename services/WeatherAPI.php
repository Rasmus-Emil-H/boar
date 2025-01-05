<?php

/**
|----------------------------------------------------------------------------
| Weather API service 
|----------------------------------------------------------------------------
|
| You can use it if you want
| However its meant to be a dummy object on how to use service providers 
|
| @author RE_WEB
| @package core
|
*/

namespace app\services;

use \app\core\src\contracts\Service;

class WeatherAPI implements Service {

    public function run(): ?string {
        return __CLASS__ . ' is alive';
    }

}