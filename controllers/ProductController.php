<?php

/**
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use \app\models\ProductModel;


class ProductController extends Controller {
    
    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        $this->setView('product');
    }

    public function edit() {
        if ($this->request->isPost()) dd(132);
        $this->isViewingValidEntity(ProductModel::class);
        $this->setView('editProduct');
    }

}