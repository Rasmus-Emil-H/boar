<?php

/*******************************
 * Bootstrap Authentication 
 * AUTHOR: RE_WEB
 * @package app\core\Authentication
*/

namespace app\core;

class Authentication {

    public function login(DbModel $user): bool {
        Application::$app->user = $user;
        $primaryKey = $user->getPrimaryKey();
        $primaryValue = $user->{$primaryKey};
        Application::$app->session->set('user', $primaryValue);
        return true;
    }

    public function logout(): void {
        Application::$app->user = null;
        Application::$app->session->removeSessionProperty('user');
    }

    public static function isGuest(): bool {
        return is_null(Application::$app->user);
    }

    public function checkUserBasedOnSession(): void {
        $primaryValue = Application::$app->session->get('user');
        if (!$primaryValue) Application::$app->user = null;
        if ($primaryValue) $this->setApplicationUser($primaryValue);
    }

    public function setApplicationUser(string $primaryValue): void {
        $authenticationClass = new Application::$app->authenticationClass();
        $primaryKey = $authenticationClass->getPrimaryKey();
        Application::$app->user = $authenticationClass->findOne([$primaryKey => $primaryValue], $authenticationClass->tableName());
    }

}