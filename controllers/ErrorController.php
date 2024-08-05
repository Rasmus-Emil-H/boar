<?php

namespace app\controllers;

use \app\core\src\Controller;

class ErrorController extends Controller {

    public function index($error) {
        $this->response->setStatusCode($error->getCode());

        $this->addStylesheet('error');
        $this->setFrontendTemplateAndData('error', ['exception' => $error, 'home' => app()->getConfig()->get('routes')->defaults->redirectTo]);
        extract($this->getData());

        require_once $this->getDataKey('header');
        require_once $this->getView();
    }

}