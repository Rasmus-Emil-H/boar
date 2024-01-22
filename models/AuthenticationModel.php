<?php

/**
 * Authentication mechanism for whatever you need
 * Use this object to authenticate with the application, external api, ect
 * AUTHOR: RE_WEB
 * @package app\models
 */

namespace app\models;

use \app\models\UserModel;
use \app\core\src\Curl;
use \app\core\src\miscellaneous\CoreFunctions;

final class AuthenticationModel {

    protected Object $data;

    public function __construct(Object $data, string $method) {
        $this->data = $data->body;
        if (!method_exists($this, $method)) throw new \app\core\src\exceptions\NotFoundException();
        $this->$method();
    }

    /**
     * Application authentication mechanism
     * @return void
     */

    public function applicationLogin() {
        $user = (new UserModel)->find('Email', $this->data->email);
        if (empty($user)) $this->invalidLogin();
        $user = CoreFunctions::first($user);
        $passwordVerify = password_verify($this->data->password, $user->get('Password'));
        if (!$passwordVerify) $this->invalidLogin();
        return $user->authenticate($user);
    }

    protected function invalidLogin() {
        CoreFunctions::app()->getResponse()->setResponse(401, ['Invalid login']);
    }

    /**
     * API authentication mechanism
     * @return array $content
     */

    public function apiLogin(): string {
        $curl = new Curl();
        foreach ($this->data as $key => $values) $curl->{"set".ucfirst($key)($values)};
        $curl->send();
        $content = $curl->content;
        $curl->close();
        return $content;
    }

}