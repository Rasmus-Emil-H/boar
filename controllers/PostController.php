<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use \app\models\PostForm;


class PostController extends Controller {

    public function ticket(Request $request, Response $response) {

        $Post = new PostForm();

        if ($request->isPost()) $this->handleSubmit($Post, $request, $response);
        
        return $this->render('posts', [
            'model' => $Post
        ]);
    }

    public function handleSubmit(PostForm $Post, Request $request, Response $response) {
        $Post->loadData($request->getBody());
        if ($Post->validate() && $Post->send()) {
            Application::$app->session->setFlashMessage('success', 'Message sent');
            $response->redirect('/');
        }
    }

}