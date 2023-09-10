<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class ErrorController extends Controller {

    public function __construct(\Exception $e) {
        Application::$app->response->setStatusCode($e->getCode());
        return $this->render('error');
    }

}