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

use \app\core\src\contracts\Service;

class ApplicationServices implements ServiceProvider {

    private const PROVIDER_DIR = '/providers';

    private array $services = [];

    public function fetchAndRunServices(): void {
        array_map(function($file) {
            $this->createObject(
                preg_replace('/' . File::PHP_EXTENSION . '/', '', $file)
            )->register();
        }, $this->getProviderDir());
    }

    public function bind(string $service): void {
       $this->setServices(new $service());
    }

    public function getServices(): array {
        return $this->services;
    }

    private function setServices(Service $service): void {
        $this->services[] = $service;
    }

    public function register(): void {}

    private function createObject(string $file): ServiceProvider {
        return (new ProviderFactory(['handler' => $file]))->create();
    }

    private function getProviderDir(): array {
        return preg_grep('/^([^.])/', scandir(app()::$ROOT_DIR . self::PROVIDER_DIR));
    }

}