<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class ErrorController extends Controller {

    public function __construct(\Throwable $e) {
        Application::$app->response->setStatusCode($e->getCode());
        $this->setView('', 'error');
        $this->setData(['exception' => $e]);
        extract($this->getData());
        require_once $this->getView();
    }

}