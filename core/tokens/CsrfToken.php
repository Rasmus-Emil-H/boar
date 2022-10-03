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

    public function __construct() {
        $this->session = new Session();
        $this->request = new Request();
    }

    public function setToken() {
        $this->session->set('CSRF_TOKEN', md5(uniqid(mt_rand(), true)));
    }

    public function getToken() {
        $token = filter_input($this->request->getBody(), 'token', FILTER_SANITIZE_STRING);

        if ( !$token || $token !== $this->session->get('CSRF_TOKEN') ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        } else {
            
        }
    }

}