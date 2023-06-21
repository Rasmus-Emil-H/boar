<?php

/*******************************
 * Bootstrap Cache 
 * AUTHOR: RE_WEB
 * @package app\core\Cache
*/

namespace app\config;

use \app\core\Application;

class Config {

    public function get(string $key): mixed {
        $config = file_get_contents(Application::$app::$ROOT_DIR.'/static/setup.json', 'R');
        return json_decode($config)->$key ?? throw new \Exception('Invalid config key');
    }

}