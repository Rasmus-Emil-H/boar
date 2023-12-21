<?php

/**
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\Controller;
use \app\core\middlewares\AuthMiddleware;
use \app\models\ProductModel;


class ProductController extends Controller {
    
    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        $this->setView('product');
    }

    public function edit() {
        if (app()->getRequest()->isPost()) dd(132);
        $this->isViewingValidEntity(ProductModel::class);
        $this->setView('editProduct');
    }

}