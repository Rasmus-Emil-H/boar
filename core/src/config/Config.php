<?php

/**
 * Bootstrap Config 
 * AUTHOR: RE_WEB
 * @package app\core\src\config
 */

namespace app\core\src\config;
use \app\core\src\miscellaneous\CoreFunctions;

class Config {

    public function get(string $key) {
        $config = file_get_contents(CoreFunctions::app()::$ROOT_DIR.'/static/setup.json');
        return json_decode($config)->$key ?? 'Key not found';
    }

}