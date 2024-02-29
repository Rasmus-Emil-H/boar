<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\gate\Gate;
use \app\models\ProductModel;


class ProductController extends Controller {

    public function index() {
        $this->setFrontendTemplateAndData(templateFile: 'product');
    }

    public function edit() {
        $product = $this->returnValidEntityIfExists();

        if (!Gate::isAuthenticatedUserAllowed('canViewProduct', $product)) $this->response->notAllowed();
        
        if ($this->request->isGet())
            return $this->setFrontendTemplateAndData(templateFile: 'editProduct', data: ["product" => $product]);
    }

}