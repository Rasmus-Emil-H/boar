<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use \app\models\ContactForm;


class ContactController extends Controller {

    public function ticket(Request $request, Response $response) {

        $contact = new ContactForm();

        if ($request->isPost()) $this->handleSubmit($contact, $request, $response);
        
        return $this->render('ticket', [
            'model' => $contact
        ]);
    }

    public function handleSubmit(ContactForm $contact, Request $request, Response $response) {
        $contact->loadData($request->getBody());
        if ($contact->validate() && $contact->send()) {
            Application::$app->session->setFlashMessage('success', 'Message sent');
            $response->redirect('/');
        }
    }

}