<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class DOMNodeController extends Controller {

    public function navbar(): self {
        $this->data['navbar'] = $this->getPartialTemplate('navbar');
        return $this;
    }

}
