<?php

namespace app\controllers;

use \app\core\src\Controller;

class ErrorController extends Controller {

    public function __construct(\Throwable $e) {
        $this->response->setStatusCode($e->getCode());
        extract($this->getData());

        $this->setFrontendTemplateAndData('error', ['exception' => $e]);
    }

}