<?php

namespace app\core\src\config;

use Exception;

class Config {

    public function get(string $key): object|string {
        $configFile = 'setup.json';

        $setup = rootDir() . '/static/' . $configFile;

        if (!is_file($setup)) {
            exit('Missing ' . $configFile);
        }

        $config = file_get_contents($setup);
        return json_decode($config)->$key ?? 'invalidEnvKey';
    }

}