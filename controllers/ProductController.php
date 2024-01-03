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

    protected string $entity = ProductModel::class;

    public function index() {
        $this->setView('product');
    }

    public function edit() {
        if ($this->request->isPost()) $this->crudEntity();
        $this->isViewingValidEntity();
        $this->setView('editProduct');
    }

}