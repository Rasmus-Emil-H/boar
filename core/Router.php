<?php

/**
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
 */

namespace app\core;

use app\core\exceptions\NotFoundException;
use app\core\factories\ControllerFactory;

class Router {

    protected const INDEX_METHOD = 'index';

    protected array $routes = [];
    protected array $queryPattern;
    protected string $method;

    public function __construct() {
        $this->queryPattern = app()->regex->validateRoute();
    }

    protected function createController() {
        if (empty($this->queryPattern)) app()->response->redirect(first(app()::$defaultRoute)->scalar);
        $handler = ucfirst($this->queryPattern[0] ?? '');
        $controller = (new ControllerFactory(['handler' => $handler]))->create();
        app()->setController($controller);
        $this->method = $this->queryPattern[1] ?? self::INDEX_METHOD;
        if (!method_exists($this->getApplicationParentController(), $this->method)) throw new NotFoundException();
    }

    protected function runMiddlewares() {
        foreach ($this->getApplicationParentController()->getMiddlewares() as $middleware) $middleware->execute();
    }

    protected function setTemplateControllers() {
        if (app()::isCLI()) return;
        $this->getApplicationParentController()->setChildren(['Header', 'Footer']);
    }

    protected function runController() {
        $controller = $this->getApplicationParentController();
        $controller->execChildData();
        $controller->{$this->method}();
    }

    protected function hydrateDOM() {
        $controller = $this->getApplicationParentController();
        extract($controller->getData(), EXTR_SKIP);
        require_once $controller->getData()['header'];
        require_once $controller->getView();    
        require_once $controller->getData()['footer'];
    }

    private function getApplicationParentController(): Controller {
        return app()->getParentController();
    }

    public function resolve() {
        $this->createController();
        $this->runMiddlewares();
        $this->setTemplateControllers();
        $this->runController();
        $this->hydrateDOM();
    }

}
