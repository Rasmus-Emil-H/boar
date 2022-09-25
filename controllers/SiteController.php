<?php

namespace app\controllers;

class SiteController extends \app\core\Controller{

    public function home() {
        $params = [1,2,3];
        return $this->render('home', $params);
    }

    public function about() {
        return $this->render('about');
    }

    public function handleContact() {
        return 'sup :)';
    }

}