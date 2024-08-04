<?php

namespace app\controllers;

use \app\core\src\Controller;

class ErrorController extends Controller {

    public function index($error) {
        $this->response->setStatusCode($error->getCode());
        $this->setView('error');
        $this->setData(['exception' => $error]);
        extract($this->getData());
        require_once $this->getView();
    }

}