<?php

/**
 * Bootstrap Cookie 
 * AUTHOR: RE_WEB
 * @package app\core\Cookie
 */

namespace app\core;

class Cookie {

    public function set(string $key, string $value): void {
        $_COOKIE[$key] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function get(string $key): string {
        $cookie = $_COOKIE[$key] ?? '';
        if ( !$cookie ) return '';
        if ( !password_verify($cookie, app()::$app->config->get('password.default')) ) throw new \Exception('Invalid cookie');
        return $_COOKIE[$key];
    }

}