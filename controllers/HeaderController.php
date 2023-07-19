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

    public function __construct() { 
        
    }

    public function index() {
        $this->setChildData(['DOMComponent:navbar'], $this);
        $this->data['flashMessage'] = Application::$app->session->getFlashMessage('success');
    }

}