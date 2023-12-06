<?php

/**
 * Authentication mechanism for whatever you need
 * Use this object to authenticate with the application, external api, ect
 * AUTHOR: RE_WEB
 * @package app\models\Authenticator
*/

namespace app\models;

use \app\core\Curl;

class Authenticator {

    protected Object $data;

    public function __construct(Object $data, string $method) {
        $this->data = $data;
        if (!method_exists($this, $method)) throw new \app\core\exceptions\NotFoundException();
        $this->$method();
    }

    /**
     * Application authentication mechanism
     * @return void
    */

    public function login() {
        if (!validateCSRF()) return false;
        $user = UserModel::search(['email' => $this->data->email]);
        if (empty($user)) return false;
        $user = first($user);
        $passwordVerify = password_verify($this->data->password, $user->get('Password'));
        if (!$passwordVerify) return;
        app()->session->set('user', $user->key());
        app()->response->redirect('/home');
    }

    /**
     * API authentication mechanism
     * @return array $content
    */

    public function api(): array {
        $curl = new Curl();
        foreach ( $this->data as $key => $values ) $curl->{"set".ucfirst($key)($values)};
        $curl->send();
        $content = $curl->content;
        $curl->close();
        return $content;
    }

}