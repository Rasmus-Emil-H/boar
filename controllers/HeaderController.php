<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class HeaderController extends Controller {

    public function index() {
        $this->data['flashMessage'] = Application::$app->session->getFlashMessage('success');
        $this->setChildren(['DOMNode:navbar']);
        $this->data['view'] = $this->getTemplatePath('header');
    }

}
