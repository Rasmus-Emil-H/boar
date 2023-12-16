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

    public function applicationLogin() {
        if (!validateCSRF()) return false;
        $user = UserModel::query()->select()->where(['email' => $this->data->email])->run();
        if (empty($user)) return false;
        $user = first($user);
        $passwordVerify = password_verify($this->data->password, $user->get('Password'));
        if (!$passwordVerify) return;
        $this->authenticateUser($user);
    }

    public function authenticateUser(UserModel $user): void {
        app()->session->set('user', $user->key());
        $sessionID = hash('sha256', uniqid());
        app()->session->set('SessionID', $sessionID);
        (new SessionModel())
            ->set(['Value' => $sessionID, 'UserID' => $user->key()])
            ->save();
        app()->response->redirect('/home');
    }

    /**
     * API authentication mechanism
     * @return array $content
     */

    public function apiLogin(): array {
        $curl = new Curl();
        foreach ($this->data as $key => $values) $curl->{"set".ucfirst($key)($values)};
        $curl->send();
        $content = $curl->content;
        $curl->close();
        return $content;
    }

}