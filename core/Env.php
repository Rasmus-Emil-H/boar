<?php

/*******************************
 * Bootstrap Env
 * Temp storage for additional keys
 * AUTHOR: RE_WEB
 * @package app\core\Env
*/

namespace app\core;

class Env {

    public function get(string $key): string {
        return $this->{$key};
    }

}