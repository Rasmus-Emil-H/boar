<?php

/**
|----------------------------------------------------------------------------
| Service trait
|----------------------------------------------------------------------------
|
| 
| @author RE_WEB
| @package \app\core\src\traits\application
|
*/

namespace app\core\src\services;

use \app\core\src\contracts\service;

use \app\core\src\providers\ServiceProvider;

class ApplicationServices implements ServiceProvider {

    /**
     * Accessibility
     */

    public function global() {
        
    }

    public function bind(string $class, callable $closure) {
        $closure($this);
    }

    /**
     * Actual services
     */

    private array $services = [];

    public function setService(Service $service): void {
        $this->services[] = $service;
    }

    public function getServices(): array {
        return $this->services;
    }

    public function register(): void {

    }

}