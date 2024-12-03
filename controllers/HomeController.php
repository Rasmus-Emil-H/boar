<?php

namespace app\controllers;

use \app\core\src\Controller;

class HomeController extends Controller {

    public function index() {
        $this->upsertChildData([
            'farm' => $this->createPartialWithData(method: 'Partial:farm', data: [
                'nested' => '🏡 I\'m a nested partial'
            ]),
            'oink' => $this->createPartialWithData(method: 'Partial:oink', data: [
                'hello' => '🐗 I\'m a partial'
            ])
        ]);

        $this->setFrontendTemplateAndData('home', ['boar' => 'Is live and running']);
    }

}