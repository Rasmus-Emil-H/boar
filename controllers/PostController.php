<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use \app\models\PostForm;


class PostController extends Controller {

    public string $defaultRoute = 'posts';

    public function posts(Request $request, Response $response) {
        return $this->render('posts', [
            
        ]);
    }

}