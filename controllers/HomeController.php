<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;


class HomeController extends Controller {

    public string $defaultRoute = 'index';

    public function index(Request $request, Response $response) {

        return $this->render('home', [
        ]);
    }

}