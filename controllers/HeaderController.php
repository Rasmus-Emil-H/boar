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
        $this->index();
    }

    public function index() {
        $this->setChildData(['DOMComponent:navbar'], $this);
        return $this->getTemplatePath('header');
    }

}