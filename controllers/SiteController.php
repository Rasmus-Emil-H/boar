<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;

class SiteController extends Controller {

    public function home() {
        $params = ['name' => 'ok'];
        return $this->render('home', $params);
    }

    public function about() {
        return $this->render('about');
    }

    public function posts() {
        return $this->render('posts');
    }

    public function handleContact(Request $request) {
        $body = $request->getBody();
        Application::$app->dump($body);
    }

}