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

class AuthenticationModel {

    protected Object $data;

    public function __construct(Object $data, string $method) {
        $this->data = $data;
        if (!method_exists($this, $method)) throw new \app\core\src\exceptions\NotFoundException();
        $this->$method();
    }

    /**
     * Application authentication mechanism
     * @return void
     */

    public function applicationLogin(): ?bool {
        if (!CoreFunctions::validateCSRF()) return false;
        $user = (new UserModel)->query()->select()->where(['email' => $this->data->email])->run();
        if (empty($user)) return false;
        $user = CoreFunctions::first($user);
        $passwordVerify = password_verify($this->data->password, $user->get('Password'));
        if (!$passwordVerify) return null;
        $user->authenticate($user);
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