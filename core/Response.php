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

    public function redirect(string $location) {
        header('Location: ' . $location);
    }
    
    public function setContentType(string $type) {
        header('Content-Type: ' . $type);
    }
    
    public function setResponse(int $code, string $content, array $message) {
        $this->setStatusCode($code);
        $this->setContentType($content);
        exit(json_encode($message));
    }

}