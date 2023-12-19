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
    protected Controller $controller;

    protected array $routes = [];
    protected array $queryPattern;
    protected string $method;

    public function __construct() {
        $this->queryPattern = app()->regex->validateRoute();
    }

    protected function checkController() {
        if (empty($this->queryPattern)) $this->getDefaultRoute();
        $handler = ucfirst($this->queryPattern[0] ?? '');
        $this->controller = (new ControllerFactory(['handler' => $handler]))->create();
        app()->setController($this->controller);
        $this->method = $this->queryPattern[1] ?? self::INDEX_METHOD;
        if (!method_exists($this->controller, $this->method)) throw new NotFoundException();
    }

    public function getDefaultRoute() {
        app()->response->redirect(app()::$defaultRoute[0]);
    }

    protected function runMiddlewares() {
        foreach ($this->controller->getMiddlewares() as $middleware) $middleware->execute();
    }

    protected function setTemplateControllers() {
        if (app()::isCLI()) return;
        $this->controller->setChildren(['Header', 'Footer']);
    }

    protected function runController() {
        $this->controller->execChildData();
        $this->controller->{$this->method}();
    }

    protected function hydrateDOM() {
        extract($this->controller->getData(), EXTR_SKIP);
        require_once $this->controller->getData()['header'];
        require_once $this->controller->getView();    
        require_once $this->controller->getData()['footer'];
    }

    public function setRequestBody() {
        $this->controller->setRequest(app()->request->clientRequest);
    }

    public function resolve() {
        $this->checkController();
        $this->setRequestBody();
        $this->runMiddlewares();
        $this->setTemplateControllers();
        $this->runController();
        $this->hydrateDOM();
    }

}
