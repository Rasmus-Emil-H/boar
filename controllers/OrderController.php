<?php

/**
 * Order Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;


class OrderController extends Controller {

    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        $this->setView('order');
        $this->setData([
            'orders' => applicationUser()->orders()->run()
        ]);
    }

    public function edit() {
        $this->setView('editOrder');
    }

}