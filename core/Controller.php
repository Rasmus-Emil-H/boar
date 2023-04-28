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

    public const MODEL_PREFIX = '\\app\\models\\';
    
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

    public function init() {
        $prefix = self::MODEL_PREFIX.ucfirst(Application::$app->request->getPHPInput()).'Model';
        Application::$app->classCheck($prefix);
        $obj = new $prefix();
        Application::$app->response->setResponse(200, 'application/json', ['msg' => $obj->init()]);
    }

    public function save() {
        foreach ( Application::$app->request->getPHPInput() as $key => $value ) {
            foreach ( $value as $objectKey => $objectValues ) {
                if ( $objectKey === 'undefined' ) continue;
                $exp = explode('-', $objectKey);
                $model = $exp[array_key_first($exp)].'Model';
                $prefix = self::MODEL_PREFIX.ucfirst($model);
                Application::$app->classCheck($prefix);
                $obj = new $prefix();
                $static = $obj->findOne([$obj->getPrimaryKey() => $exp[1]], $obj->tableName());
                if(!$static) {
                    $static = new $prefix();
                    $pk = $static->getPrimaryKey();
                    $static->{$pk} = $exp[1];
                }
                $static->setAttributes((array)$objectValues);
			    $static->save();
            }
        }
		Application::$app->response->setResponse(200, 'application/json', ['msg' => 'saved']);
	}

    public function remove() {
		$id = Application::$app->request->getPHPInput();
		$reqModel = explode('-', $id->id);
		$model = $reqModel[0].'Model';
        $prefix = self::MODEL_PREFIX.ucfirst($model);
        Application::$app->classCheck($prefix);
        $obj = new $prefix();
		$static = $obj->findOne([$obj->getPrimaryKey() => $reqModel[1]], $obj->tableName());
		$static->remove();
        Application::$app->response->setResponse(204, 'application/json', ['msg' => 'deleted']);
	}

}