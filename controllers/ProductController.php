<?php

/**
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\ProductModel;


class ProductController extends Controller {

    public function index() {
        $this->setView('product');
    }

    public function edit() {
        if ($this->request->isPost()) CoreFunctions::dd(132);
        $this->isViewingValidEntity(ProductModel::class);
        $this->setView('editProduct');
    }

}