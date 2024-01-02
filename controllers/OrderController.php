<?php

/**
 * Order Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use \app\core\src\miscellaneous\CoreFunctions;

class OrderController extends Controller {

    public function index() {
        $this->setView('order');
        $this->setData([
            'orders' => CoreFunctions::applicationUser()->orders()->run()
        ]);
    }

    public function edit() {
        $this->setView('editOrder');
    }

}