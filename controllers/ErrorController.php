<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class ErrorController extends Controller {

    public function __construct(\Throwable $e) {
        Application::$app->response->setStatusCode($e->getCode());
        $this->setData(['exception' => $e]);
        return $this->render('error');
    }

}