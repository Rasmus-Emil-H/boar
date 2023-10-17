<?php

/**
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
*/

namespace app\core;

use \app\core\middlewares\Middleware;
use \app\core\exceptions\NotFoundException;

class Controller {

    private const DEFAULT_METHOD = 'index';
    private const INVALID_METHOD_TEXT = 'Invalid method';
    private const INVALID_CONTROLLER_TEXT = 'Invalid controller';
    private const PARTIALS_TEXT = '/views/partials/';
    private const INVALID_VIEW = 'Invalid view';

    protected object $request;

    /**
     * @var string $currentAction
    */

    public string $action = '';

    /**
     * @var array Current \app\core\controller | [Children] data
    */

    protected $data = [];


    /**
     * @var string $view
     */

    protected string $view = '';

    /*
     * Default layout
    */

    public string $layout = 'main';

    /*
     * Support for additional controller logic, partials
    */

    protected array $children = [];

    /**
     * Set data in current controller
     * @var array|object [\Controller]
     * @return void
    */

    public function setData($data): void {
      $merged = array_merge($this->getData(), $data);
      $this->data = $merged;
    }

    /**
     * get data in current controller
     * @return array
    */

    public function getData(): array {
        return $this->data;
    }
    
    /**
     * Array of middleware classes
     * @var app\core\middlewares\Middleware[]
    */

    protected array $middlewares = [];

    public function setChildren(array $children): void {
      foreach ( $children as $child ) $this->children[] = $child; 
    }

    /**
     * @param object $request
     */

    public function setRequest(object $request) {
      $this->request = $request;
    }

    /**
     * return object $request 
     */

    protected function getRequest() {
      return $this->request;
    }

    /**
     * Get data from child
     * Then set data on instantiated controller
     * @param array [strings of to be \app\core\Controller]
     * @param \app\core\controller Parent controller
     * @return void
    */

    public function setChildData(array $childControllers): void {
      foreach ( $childControllers as $childKey => $childController ) {
        [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
        $cController = '\\app\controllers\\'.$controller.'Controller';
        app()->classCheck($cController);
        $static = new $cController();
        $static->{$method}();
        app()->controller->setData($static->getData());
        $static->execChildData();
      }
    }

    public function getView(): string {
        return $this->view ?? self::INVALID_VIEW;
    }

    protected function setView(string $dir, string $view) {
      $this->view = $this->getTemplatePath($dir, $view);
    }

    /**
     * Get names of children controllers
     * @return array
    */

    public function getChildren(): array {
        return $this->children;
    }

    public function execChildData() {
      $this->setChildData($this->getChildren());
    }

    public function getPartialTemplate(string $partial): string {
      return $this->getTemplatePath('partials/', $partial);
    }

    public function getTemplate(string $partial): string {
      return $this->getTemplatePath('', $partial);
    }
    /**
     * @param string template name
     * @return string
    */

    public function getTemplatePath(string $folder, string $template): string {
        return app()::$ROOT_DIR .  '/views/' . $folder . $template . '.tpl.php';
    }

    /**
     * Render view based on data
     * @return void
    */

    public function render(string $view): void {
        app()->view->renderView();
    }

    /**
     * Set layout for the current controller
     * @return void
    */

    public function setLayout(string $layout): void {
        $this->layout = $layout;
    }

    /**
     * Set middlewares for the current controller
     * @return void
    */

    public function registerMiddleware(Middleware $middleware): void {
        $this->middlewares[] = $middleware;
    }   

    /**
     * @return [\app\core\Middleware]
    */

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

}
