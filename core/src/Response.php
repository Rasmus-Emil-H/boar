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
    
    public function setResponse(int $code, array $message = []) {
        if (empty($message)) $message = $this->returnStdSuccesMessage();

        $this->setStatusCode($code);
        $this->setContentType('application/json');
        
        exit(json_encode($message));
    }

    public function returnStdSuccesMessage(): array {
        return ['responseJSON' => 'Success'];
    }

    public function returnMessage(string $message): array {
        return [$message];
    }

    public function notFound(string $message) {
        $this->setResponse(404, [$message]);
    }

    public function badToken() {
        $this->setResponse(400, $this->returnMessage('Bad token'));
    }

    public function dataConflict() {
        $this->setResponse(409, $this->returnMessage('Invalid input. Please try something else'));
    }

    public function requestLimitReached() {
        $this->setResponse(429, $this->returnMessage('Too many requests')); 
    }

    public function methodNotAllowed() {
        $this->setResponse(405, $this->returnMessage('Method not allowed'));
    }

}