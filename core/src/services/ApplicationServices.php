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

use \app\core\src\factories\ProviderFactory;

use \app\core\src\File;

use \app\core\src\providers\ServiceProvider;

class ApplicationServices implements ServiceProvider {

    private const PROVIDER_DIR = '/providers';

    public function fetchAndRunServices(): void {
        array_map(function($file) {
            $this->createObject($file)->register();
        }, $this->getProviderDir());
    }

    private function createObject(string $file): ServiceProvider {
        return (new ProviderFactory(['handler' => preg_replace('/' . File::PHP_EXTENSION . '/', '', $file)]))->create();
    }

    private function getProviderDir(): array {
        return preg_grep('/^([^.])/', scandir(app()::$ROOT_DIR . self::PROVIDER_DIR));
    }

    private array $services = [];

    public function bind(string $class, $callback): void {
        $t = $this->createObject($class);var_dump($t);
    }

    public function getServices(): array {
        return $this->services;
    }

    public function register(): void {}

}