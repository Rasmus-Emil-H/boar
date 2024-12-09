<?php

namespace app\core\src\database\adapters;

abstract class Adapter {

    protected string $driver = 'mysql';
    protected object $config;

    public function connect($config) {
        $this->config = $config;

        return $this->doConnect($config);
    }

    public function getDriverName() {
        return $this->driver;
    }

    public function getConfig() {
        return $this->config;
    }

    public function getConfigValue($name, $default = null) {
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    abstract protected function doConnect();

}