<?php

namespace app\controllers;

use \app\core\src\Controller;

class ApiController extends Controller {

    public function index() {
        return $this->response->ok('Hello from ' . __CLASS__);
    }

}