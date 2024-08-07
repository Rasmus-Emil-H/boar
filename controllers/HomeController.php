<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;

class HomeController extends Controller {

    public function index() {
        $this->upsertChildData([
            'farm' => $this->createPartialWithData('Partial:farm', [
                'nested' => '🏡 I\'m a nested partial'
            ]),
            'oink' => $this->createPartialWithData('Partial:oink', [
                'hello' => '🐗 I\'m a partial'
            ])
        ]);
        
        $this->setFrontendTemplateAndData('home', ['boar' => 'Is live and running']);
    }

}