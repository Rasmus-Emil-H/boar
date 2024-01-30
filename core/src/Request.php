<?php

/**
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
 */

namespace app\core\src;

use \app\core\Application;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\utilities\Utilities;

class Request {

    private object $requestConfig;
    private array $args = [];
    public object $clientRequest;
    
    private const SECONDS_THROTTLER = 60;
    private const METHOD_GET = 'get';
    private const METHOD_POST = 'post';
    private const REQUEST_MADE_KEY = 'requestsMade';
    private const INITIAL_INDEX_ATTEMPT = '-0';

    protected string $allowedRequestAmount;
    protected string $allowedRequestMinutes;
    protected string $requestAttempts;
    protected string $attempts;
    protected string $allowedSecondsForRequestInterval;
    protected string $subtractedSeconds;

    public function __construct(
        protected Application $app
    ) {
        $this->clientRequest = $this->getCompleteRequestBody();
        $this->setArguments();
        $this->requestConfig = $this->app->getConfig()->get('request')->limit;
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
        return strtolower($this->getServerInformation()['REQUEST_METHOD'] ?? self::METHOD_GET);
    }

    public function isGet(): bool {
        return $this->method() === self::METHOD_GET;
    }

    public function isPost(): bool {
        return $this->method() === self::METHOD_POST;
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
        $type = $this->method() === self::METHOD_GET ? INPUT_GET : INPUT_POST;
        foreach ($_REQUEST as $key => $_) {
            if (is_array($_)) foreach ($_ as $k => $v) $body[$key][] = (string)$v;
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
        if ($this->app::isCLI()) return;
        $this->setRatelimiting();
        $this->checkRateLimit();
    }

    protected function setRatelimiting() {
        $this->allowedRequestMinutes = $this->requestConfig->minutes;
        $this->allowedRequestAmount  = $this->requestConfig->amount;
    }

    protected function checkRateLimit() {
        $app = $this->app;
        $session = $app->getSession();
        $this->requestAttempts = $session->get(self::REQUEST_MADE_KEY);
        if (!$this->requestAttempts) return;
        $this->validateCurrentSessionRateLimit();
        $this->handleCurrentSessionRateLimit();
    }

    protected function validateCurrentSessionRateLimit() {
        $this->updateCurrentSessionRateLimit();
        list($initialUnixSessionRateLimitInstance, $requestAttemptCounter) = explode('-', $this->requestAttempts);
        $this->subtractedSeconds = (strtotime('now') - (int)$initialUnixSessionRateLimitInstance);
        $this->app->getSession()->set(self::REQUEST_MADE_KEY, str_replace(('-'.$requestAttemptCounter), ('-'.($requestAttemptCounter+1)), $this->requestAttempts));
    }

    protected function handleCurrentSessionRateLimit() {
        if ($this->requestAttempts > $this->allowedRequestAmount) $this->app->getResponse()->requestLimitReached();
        if ($this->subtractedSeconds > $this->allowedSecondsForRequestInterval) $this->app->getSession()->set(self::REQUEST_MADE_KEY, $this->attempts);
    }

    protected function updateCurrentSessionRateLimit() {
        $this->allowedSecondsForRequestInterval = ($this->allowedRequestMinutes * self::SECONDS_THROTTLER);
        $this->attempts = ((string)strtotime('+'.$this->allowedRequestMinutes.' minutes') . self::INITIAL_INDEX_ATTEMPT);
        if (!$this->requestAttempts) $this->app->getSession()->set(self::REQUEST_MADE_KEY, $this->attempts);
    }

}