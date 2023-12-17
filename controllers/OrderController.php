<?php

/**
 * Order Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;


class OrderController extends Controller {

    protected const DEFAULT_VIEW = 'order';

    public string $defaultRoute = 'index';

    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        $this->setView('', 'order');
        $this->setData([
            'orders' => applicationUser()->orders()->run()
        ]);
    }

    public function edit(): self {
        $this->data['order'] = $this->getTemplatePath('/', 'order');
        return $this;
    }

}