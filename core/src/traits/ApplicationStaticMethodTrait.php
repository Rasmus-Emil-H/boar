<?php

namespace app\core\src\traits;

use app\models\UserModel;

trait ApplicationStaticMethodTrait {
    
    /**
    |----------------------------------------------------------------------------
    | Static methods
    |----------------------------------------------------------------------------
    |
    */

    public static function isCLI(): bool {
        return php_sapi_name() === 'cli';     
    }

    public static function isGuest(): bool {
        return !(new UserModel())->hasActiveSession();
    }

    public function getUser(): ?UserModel {
        if (!$this->session->get('user')) return null;
        return new UserModel($this->session->get('user'));
    }

    public static function isDevSite(): bool {
        return self::$app->config->get('inDevelopment') === true;
    }

}