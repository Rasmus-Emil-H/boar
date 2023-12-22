<?php

/**
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
 */

namespace app\core\src;

class Request {

    private array $args = [];
    public  object $clientRequest;

    public function __construct() {
        $this->setArguments();
        $this->clientRequest = $this->getCompleteRequestBody();
    }

    public function getPath(): string {
        $path = $this->clientRequest->server['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if(!$position) return $path;
        return substr($path, 0, $position);
    }

    public function setArguments(): void {
        $this->args = explode('/', trim($this->getPath(), '/'));
    }

    public function getArguments(): array {
        return $this->args;
    }
    
    public function getArgument(int $index): mixed {
        return $this->getArguments()[$index] ?? throw new \app\core\exceptions\NotFoundException('Index was not found');
    }
    
    public function getReferer(): string {
        return $this->clientRequest->server['HTTP_REFERER'];
    }
    
    public function getHost(): string {
        return $this->clientRequest->server['HTTP_HOST'];
    }
    
    public function getQueryParams(): array {
        return $_GET;
    }
    
    public function getReplacedHost(): string {
        return str_replace($this->getHost(), '', $this->getReferer());
    }
    
    public function getAdditionalParams(array $indexes): array {
        $indexes = [];
        return $indexes;
    }

    public function method(): string { 
        return $this->clientRequest->server['REQUEST_METHOD'] ?? 'get';
    }

    public function isGet(): bool {
        return $this->method() === 'get';
    }

    public function isPost(): bool {
        return $this->method() === 'post';
    }

    public function getCompleteRequestBody() {
        $obj = ["files" => $_FILES, "server" => $_SERVER, "cookie" => $_COOKIE, 'path' => $this->getPath()];
        $obj['body'] = $this->getBody();
        return (object)$obj;
    }

    public function getBody(): object {
        $body = [];

        if ($this->method() === 'get') 
            foreach ($_GET as $key => $value) 
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->method() === 'post') 
            foreach ($_POST as $key => $value) 
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);

        return (object)$body;
    }

    public function getIP() {
        return $this->clientRequest->server['REMOTE_ADDR'] ?? php_sapi_name();
    }
    
    public function getPHPInput() {
        return json_decode(file_get_contents('php://input'));
    }

}