<?php

/**
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
 */

namespace app\core\src;

use app\core\src\miscellaneous\CoreFunctions;

class Request {

    private array $args = [];
    public object $clientRequest;

    public function __construct() {
        $this->clientRequest = $this->getCompleteRequestBody();
        $this->setArguments();
        $this->checkAmountOfRequest();
    }

    public function getPath(): string {
        $path = $this->clientRequest->server['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if (!$position) return $path;
        return substr($path, 0, $position);
    }

    public function setArguments(): void {
        $this->args = explode('/', trim($this->getPath(), '/'));
    }

    public function getArguments(): array {
        return $this->args;
    }
    
    public function getArgument(int|string $index): mixed {
        return CoreFunctions::getIndex($this->args, $index);
    }
    
    public function getReferer(): string {
        return $this->clientRequest->server['HTTP_REFERER'];
    }
    
    public function getHost(): string {
        return $this->clientRequest->server['HTTP_HOST'];
    }

    public function method(): string {
        return strtolower($this->clientRequest->server['REQUEST_METHOD'] ?? 'get');
    }

    public function isGet(): bool {
        return $this->method() === 'get';
    }

    public function isPost(): bool {
        return $this->method() === 'post';
    }

    public function getCompleteRequestBody() {
        $obj = ["files" => $_FILES, "server" => $_SERVER, "cookie" => $_COOKIE, 'body' => $this->getBody()];
        return (object)$obj;
    }

    public function getBody(): object {
        $body = [];        
        $type = $this->method() === 'get' ? INPUT_GET : INPUT_POST;
        foreach ($_REQUEST as $key => $value) $body[$key] = filter_input($type, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        return (object)$body;
    }

    public function getIP() {
        return $this->clientRequest->server['REMOTE_ADDR'] ?? php_sapi_name();
    }
    
    public function getPHPInput() {
        return json_decode(file_get_contents('php://input'));
    }

    private function checkAmountOfRequest() {
        $app = CoreFunctions::app();
        $session = $app->getSession();
        $allowedRequestMinutes = $app->getConfig()->get('request')->{429}->minutes;
        $allowedRequestAmount = $app->getConfig()->get('request')->{429}->amount;
        $attempts = ((string)strtotime('+'.$allowedRequestMinutes.' minutes').'-0');
        $requestAttemps = $session->get('requestsMade');
        $allowedSecondsForRequestInterval = ($allowedRequestMinutes*60);
        if (!$requestAttemps) $session->set('requestsMade', $attempts);
        list($time, $attempsCounter) = explode('-', $requestAttemps);
        $subtractedSeconds = (strtotime('now') - (int)$time);
        $session->set('requestsMade', str_replace(('-'.$attempsCounter), ('-'.($attempsCounter+1)), $requestAttemps));
        if ($requestAttemps > $allowedRequestAmount) $app->getRequest()->requestLimitReached();
        if ($subtractedSeconds > $allowedSecondsForRequestInterval) $session->set('requestsMade', $attempts);
    }

}