<?php

/*******************************
 * Default, built in, encryption methods
 * AUTHOR: RE_WEB
 * @package app\models\Authenticator
*/

namespace app\core\encryption;

class Encryption {

    public static function encrypt(mixed $value): string {
        return openssl_encrypt($value, 'aes-256-ctr', app()->getConfig()->get('encryption')->openSSL->key, 1, app()->getConfig()->get('encryption')->openSSL->iv);
    }
    
    public static function decrypt(mixed $value): string {
        return openssl_decrypt($value, 'aes-256-ctr', app()->getConfig()->get('encryption')->openSSL->key, 1, app()->getConfig()->get('encryption')->openSSL->iv);
    }

}