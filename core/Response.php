<?php

/*******************************
 * Bootstrap Response 
 * AUTHOR: RE_WEB
 * @package app\core\Response
*/

namespace app\core;

class Response {

    public function setStatusCode(int $code) {
        http_response_code($code);
    }

}