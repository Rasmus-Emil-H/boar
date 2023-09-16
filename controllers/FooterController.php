<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class FooterController extends Controller {

  public function index() {
    $this->setView('partials/', 'footer');
    return $this;
  }

}
