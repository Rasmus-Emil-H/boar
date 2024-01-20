<?php

/**
 * Bootstrap Response 
 * AUTHOR: RE_WEB
 * @package app\core\src\Response
 */

namespace app\core\src;

final class Response {

    public function setStatusCode(int $code) {
        http_response_code($code);
    }

    public function redirect(string $location) {
        header('Location: ' . $location);
    }
    
    public function setContentType(string $type) {
        header('Content-Type: ' . $type);
    }
    
    public function setResponse(int $code, array $message) {
        $this->setStatusCode($code);
        $this->setContentType('application/json');
        exit(json_encode($message));
    }

    public function notFound(string $message) {
        $this->setResponse(404, [$message]);
    }

    public function badToken() {
        $this->setResponse(400, ['Bad token']);
    }

    public function dataConflict() {
        $this->setResponse(409, ['Invalid input. Please try something else']);
    }

    public function requestLimitReached() {
        $this->setResponse(429, ['Too many requests']); 
    }

}