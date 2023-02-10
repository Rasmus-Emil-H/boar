<?php

/*******************************
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
*/

namespace app\core;

use \app\core\middlewares\Middleware;

class Controller {

    public string $layout = 'main';
    
    /**
     * Array of middleware classes
     * @var app\core\middlewares\Middleware[]
    */
    protected array $middlewares = [];

    /**
     * @var string $currentAction 
    */
    public string $action = '';

    public function render(string $view, array $params = []) {
        echo Application::$app->view->renderView($view, $params);
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

    public function remove(): void {
		$id = Application::$app->request->getPHPInput();
		$reqModel = explode('-', $id->id);
		$model    = $reqModel[0].'Model';
		$static   = $this->{$model}->findOne([$this->{$model}->getPrimaryKey() => $reqModel[1]], $this->{$model}->tableName());
		$static->remove();
	}

}