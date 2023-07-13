<?php

/*******************************
 * Authentication mechanism for whatever you need
 * Use this object to authenticate with the application, external api, ect
 * AUTHOR: RE_WEB
 * @package app\models\Authenticator
*/

namespace app\models;

use \app\core\Application;
use \app\core\Curl;

class Authenticator {

    protected array $data;

    public function __construct(array $data, string $method) {
        $this->data = $data;
        if (!method_exists($this, $method)) throw new \Exception('Invalid method');
        $this->$method();
    }

    /**
     * Application authentication mechanism 
     * @return void
    */

    public function login() {
        $status = false;
        $user = UserModel::search(['email' => $this->data['email']]);
        if(!empty($user)) {
            $user = $user[array_key_first($user)];
            $status = password_verify($this->data['password'], $user->get('Password'));
            if($status) Application::$app->session->set('user', $user->key());
        }
        Application::$app->response->setResponse(200, 'application/json', ['message' => $status]);
    }

    /**
     * API authentication mechanism
     * @return array $data
    */

    public function api(): array {
        $curl = new Curl();
        foreach ( $this->data as $key => $values ) $curl->{"set".ucfirst($key)($values)};
        $curl->send();
        return (array)$curl->content;
    }

}