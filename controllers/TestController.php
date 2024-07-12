<?php

namespace app\controllers;

use \app\core\src\Controller;

final class TestController extends Controller {

    public function index() {
        return $this->setFrontendTemplateAndData('Test', []);
    }

}