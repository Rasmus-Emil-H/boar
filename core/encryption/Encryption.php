<?php

/*******************************
 * Default, built in, encryption methods
 * AUTHOR: RE_WEB
 * @package app\models\Authenticator
*/

namespace app\core\encryption;

use \app\core\Application;

class Encryption {

    public static function encrypt(mixed $value): string {
        return openssl_encrypt($value, 'aes-256-ctr', Application::$app->config->get('encryption')->openSSL->key, 1, Application::$app->config->get('encryption')->openSSL->iv);
    }
    
    public static function decrypt(mixed $value): string {
        return openssl_decrypt($value, 'aes-256-ctr', Application::$app->config->get('encryption')->openSSL->key, 1, Application::$app->config->get('encryption')->openSSL->iv);
    }

}