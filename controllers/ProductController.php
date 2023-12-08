<?php

/**
 * Product Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use app\core\Controller;
use app\core\middlewares\AuthMiddleware;


class ProductController extends Controller {

    protected const DEFAULT_VIEW = 'product';

    public string $defaultRoute = 'index';

    public function __construct() { 
       $this->registerMiddleware(new AuthMiddleware()); 
    }

    public function index() {
        return $this->render(self::DEFAULT_VIEW, [

        ]);
    }

    public function edit(): self {
        $this->data['product'] = $this->getTemplatePath('/', 'product');
        return $this;
    }

}