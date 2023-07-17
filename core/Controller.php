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

    /**
     * @var string $currentAction
    */

    public string $action = '';

    /**
     * @var array Variable data generated by extending controllers.
    */

    protected $data = [];

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
     * @return void
    */

    public function setData(array $data): void {
        $this->data = $data;
    }

    /**
     * get data in current controller
     * @return void
    */

    public function getData(): array {
        return $this->data;
    }
    
    /**
     * Array of middleware classes
     * @var app\core\middlewares\Middleware[]
    */

    protected array $middlewares = [];

    /**
     * Get data from child
     * Then set data on called controller
     * @param array [strings of to be \app\core\Controller]
     * @param \app\core\controller Parent controller
     * @return void
    */

    protected function setChildData(array $childControllers, Controller $currentObject): void {
        foreach ( $childControllers as $childController ) {
            [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = '\\app\controllers\\'.$controller.'Controller';
            if (!class_exists($cController)) throw new NotFoundException(self::INVALID_CONTROLLER_TEXT);
            if (!method_exists($cController, $method)) throw new NotFoundException(self::INVALID_METHOD_TEXT);
            $static = (new $cController())->{$method}();
            $currentObject->setData($static->getData());
        }
    }

    /**
     * Get names of children controllers
     * @return array
    */

    public function getChildren() : array {
        return $this->children;
    }

    /**
     * @param string template name
     * @return string
    */

    public function getTemplatePath(string $template): string {
        $view = Application::$ROOT_DIR . self::PARTIALS_TEXT . $template . '.tpl.php';
        return $view;
    }

    public function render(string $view) {
        echo Application::$app->view->renderView($view, $this->data);
    }

    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

    public function registerMiddleware(Middleware $middleware) {
        $this->middlewares[] = $middleware;
    }   

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

}