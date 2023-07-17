<?php

/*******************************
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;


class UserController extends Controller {

    protected const DEFAULT_VIEW = 'profile';

    public string $defaultRoute = 'index';

    public function __construct() { 
        
    }

    public function index() {

        return $this->render(self::DEFAULT_VIEW, [

        ]);
    }

    public function edit() {
        $this->data['product'] = $this->getTemplatePath('product');
    }

}