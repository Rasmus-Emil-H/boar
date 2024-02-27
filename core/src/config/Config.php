<?php

namespace app\core\src\config;

class Config {

    public function get(string $key) {
        $config = file_get_contents(app()::$ROOT_DIR.'/static/setup.json');
        return json_decode($config)->$key ?? 'Key not found';
    }

}