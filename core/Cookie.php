<?php

/*******************************
 * Bootstrap Cookie 
 * AUTHOR: RE_WEB
 * @package app\core\Cookie
*/

namespace app\core;

class Cookie {

    public function setCookie(string $key, string $value): void {
        $_COOKIE[$key] = $value;
    }

    public function getCookie(string $key): string {
        return $_COOKIE[$key];
    }

}