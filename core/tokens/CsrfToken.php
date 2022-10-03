<?php

/*******************************
 * Bootstrap CsrfToken 
 * AUTHOR: RE_WEB
 * @package app\core\CsrfToken
*/

namespace app\core\tokens;

use app\core\Session;
use app\core\Request;

class CsrfToken {

    protected Session $session;
    protected Request $request;
    protected Response $response;

    public function __construct() {
        $this->session = Application::$app->session;
        $this->request = Application::$app->request;
        $this->response = Application::$app->response;
    }

    public function setToken() {
        $this->session->set('CSRF_TOKEN', md5(uniqid(mt_rand(), true)));
    }

    public function getToken() {
        
        $token = filter_input($this->request->getBody(), 'token', FILTER_SANITIZE_STRING);

        if ( !$token || $token !== $this->session->get('CSRF_TOKEN') ) 
            $this->response->setStatusCode(405);
    }

}