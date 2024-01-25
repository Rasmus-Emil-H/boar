<?php

/**
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
 */

namespace app\core\src;

use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\utilities\Utilities;

class Request {

    private object $requestConfig;
    private array $args = [];
    public object $clientRequest;

    public function __construct() {
        $this->clientRequest = $this->getCompleteRequestBody();
        $this->setArguments();
        $this->requestConfig = CoreFunctions::app()->getConfig()->get('request')->limit;
        $this->checkAmountOfRequest();
    }

    public function getPath(): string {
        $path = $this->getServerInformation()['REQUEST_URI'] ?? '/';
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
        return $this->getServerInformation()['HTTP_REFERER'];
    }
    
    public function getHost(): string {
        return $this->getServerInformation()['HTTP_HOST'];
    }

    public function method(): string {
        return strtolower($this->getServerInformation()['REQUEST_METHOD'] ?? 'get');
    }

    public function isGet(): bool {
        return $this->method() === 'get';
    }

    public function isPost(): bool {
        return $this->method() === 'post';
    }

    public function getCompleteRequestBody() {
        $obj = ['files' => $_FILES, 'body' => $this->getBody()];
        return (object)$obj;
    }

    public function getServerInformation() {
        return $_SERVER;
    }

    public function getBody(): object {
        $body = [];        
        $type = $this->method() === 'get' ? INPUT_GET : INPUT_POST;
        foreach ($_REQUEST as $key => $_) {
            if (is_array($_)) foreach ($_ as $k => $v) $body[$key][] = Utilities::stdFilterSpecialChars($type, $k);
            else $body[$key] = Utilities::stdFilterSpecialChars($type, $key);
        }
        return (object)$body;
    }

    public function getIP() {
        return $this->getServerInformation()['REMOTE_ADDR'] ?? php_sapi_name();
    }
    
    public function getPHPInput() {
        return json_decode(file_get_contents('php://input'));
    }

    private function checkAmountOfRequest() {
        $app = CoreFunctions::app();
        if ($app::isCLI()) return;
        $session = $app->getSession();
        $allowedRequestMinutes = $this->requestConfig->minutes;
        $allowedRequestAmount  = $this->requestConfig->amount;
        $attempts = ((string)strtotime('+'.$allowedRequestMinutes.' minutes').'-0');
        $requestAttemps = $session->get('requestsMade');
        if (!$requestAttemps) return;
        $allowedSecondsForRequestInterval = ($allowedRequestMinutes*60);
        if (!$requestAttemps) $session->set('requestsMade', $attempts);
        list($time, $attempsCounter) = explode('-', $requestAttemps);
        $subtractedSeconds = (strtotime('now') - (int)$time);
        $session->set('requestsMade', str_replace(('-'.$attempsCounter), ('-'.($attempsCounter+1)), $requestAttemps));
        if ($requestAttemps > $allowedRequestAmount) $app->getResponse()->requestLimitReached();
        if ($subtractedSeconds > $allowedSecondsForRequestInterval) $session->set('requestsMade', $attempts);
    }

}