<?php

/*******************************
 * Bootstrap Cookie 
 * AUTHOR: RE_WEB
 * @package app\core\Cookie
*/

namespace app\core;

class Cookie {

    public function setCookie(string $key, string $value): void {
        $_COOKIE[$key] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function getCookie(string $key): string {
        $cookie = $_COOKIE[$key] ?? '';
        if ( !password_check($cookie, PASSWORD_DEFAULT) ) exit('Invalid cookie');
        return $_COOKIE[$key];
    }

}