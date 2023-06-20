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
        $this->$method();
    }

    public function login() {
        // $user = UserModel::search(['email' => $this->data['email']]);
        // if (!$user || password_verify($this->data['password'], $user->password)) return false;
        // return Application::$app->authentication->login($user);
    }

    public function api() {

    }

}