<?php

/**
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
 */

namespace app\core;

use app\core\exceptions\NotFoundException;

class Router {

    protected const CONTROLLER   = 'Controller';
    protected const INDEX_METHOD = 'index';

    protected string $method;

    protected array $routes = [];
    protected array $queryPattern;

    public function __construct() {
        $this->queryPattern = app()->regex->validateRoute();
    }

    protected function checkController() {
        if (empty($this->queryPattern)) $this->getDefaultRoute();
        $handler = ucfirst($this->queryPattern[0] ?? '').self::CONTROLLER;
        $controller = '\\app\controllers\\'.$handler;
        app()->classCheck($controller);
        $currentController = new $controller();
        app()->setController($currentController);
        $method = $this->queryPattern[1] ?? self::INDEX_METHOD;
        if (!method_exists(app()->controller, $method)) throw new NotFoundException();
        $this->method = $method;
    }

    public function getDefaultRoute() {
        app()->response->redirect(app()::$defaultRoute['login']);
    }

    protected function runMiddlewares() {
        foreach (app()->controller->getMiddlewares() as $middleware) $middleware->execute();
    }

    protected function setTemplateControllers() {
      if (app()::isCLI()) return;
      app()->controller->setChildren(['Header', 'Footer']);
    }

    protected function runController() {
        app()->controller->execChildData();
        app()->controller->{$this->method}();
    }

    protected function hydrateDOM() {
        extract(app()->controller->getData(), EXTR_SKIP);
        require_once app()->controller->getData()['header'];
        require_once app()->controller->getView();    
        require_once app()->controller->getData()['footer'];
    }

    public function setRequest() {
        app()->controller->setRequest(app()->request->getCompleteRequestBody());
    }

    public function resolve() {
        $this->checkController();
        $this->setRequest();
        $this->runMiddlewares();
        $this->setTemplateControllers();
        $this->runController();
        $this->hydrateDOM();
    }

}
