<?php

/**
 * API Controller 
 * 
 * Used for on-demand data exchange
 * 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;

class ApiController extends Controller {

    public function index() {
        return $this->response->ok('Hello from ' . __CLASS__);
    }

}