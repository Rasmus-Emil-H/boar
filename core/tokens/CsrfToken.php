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
        $this->session->set('csrf', $this->generateRandom());
    }

    public function getToken(): string {
        if (!Application::$app->session->get('csrf')) $this->setToken();
        return Application::$app->session->get('csrf');
    }

    protected function generateRandom(): int {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

}