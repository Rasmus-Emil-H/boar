<?php

/*******************************
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class DOMComponentController extends Controller {

    public function navbar(): self {
        $this->data['navbar'] = $this->getTemplatePath('navbar');
        return $this;
    }

}