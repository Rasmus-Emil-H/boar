<?php

/*******************************
 * Authentication mechanism for whatever you need 
 * AUTHOR: RE_WEB
 * @package app\models\Authenticator
*/

namespace app\models;

use \app\core\Application;

class Authenticator {

    protected array $data;

    public function __construct(array $data, string $method) {
        $this->data = $data;
        if (!method_exists($this, $method)) throw new \Exception('Invalid method');
        $this->$method();
    }

    public function login() {
        $user = UserModel::search(['email' => $this->data['email']]);
        var_dump($user);exit;
        if (!$user || password_verify($this->data['password'], $user->password)) return false;
        // return Application::$app->session->set('qwd', $user);
    }

    public function api() {

    }

}