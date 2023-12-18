<?php

/**
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\models\UserModel;


class ProductController extends Controller {
    
    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        $this->setView('', 'product');
    }

    public function edit() {
        if (app()->request->isPost()) dd(132);
        if (!$this->isViewingValidEntity(UserModel::class)) throw new \app\core\exceptions\NotFoundException('Invalid entity');
        $this->setView('', 'editProduct');
    }

}