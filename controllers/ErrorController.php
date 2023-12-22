<?php

namespace app\controllers;

use \app\core\Controller;

class ErrorController extends Controller {

    public function __construct(\Throwable $e) {
        app()->getResponse()->setStatusCode($e->getCode());
        $this->setView('error');
        $this->setData(['exception' => $e]);
        extract($this->getData());
        require_once $this->getView();
    }

}