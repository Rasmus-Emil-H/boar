<?php

/*******************************
 * Bootstrap CsrfToken 
 * AUTHOR: RE_WEB
 * @package app\core\CsrfToken
*/

namespace app\core\tokens;

use app\core\Session;
use app\core\Request;
use app\core\Response;
use app\core\Application;

class CsrfToken {

    protected Session $session;
    protected Request $request;
    protected Response $response;

    public function __construct() {
        $this->session = Application::$app->session;
        $this->request = Application::$app->request;
        $this->response = Application::$app->response;
    }

    public function setToken(): void {
        $this->session->set('CSRF_TOKEN', $this->generateRandom());
    }

    protected function generateRandom(): int {
        return bin2hex(md5(uniqid(mt_rand(), true)) . random_int(rand(200, 20000000000), rand(4000, 70000000000)));
    }

    public function getToken(): void {
        $token = filter_input($this->session->get('CSRF_TOKEN'), 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( !$token || $token !== $this->session->get('CSRF_TOKEN') ) $this->response->setStatusCode(405);
    }

}