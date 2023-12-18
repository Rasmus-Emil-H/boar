<?php

/*******************************
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
*/

namespace app\core;

class Request {

    private array $args = [];

    public function __construct() {
        $this->setArguments();
    }

    public function getPath(): string {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if(!$position) return $path;
        return substr($path, 0, $position);
    }

    public function setArguments() {
        $this->args = explode('/', ltrim($this->getPath(), '/'));
    }

    public function getArguments() {
        return $this->args;
    }
    
    public function getRefere(): string {
        return htmlspecialchars($_SERVER['HTTP_REFERER']);
    }
    
    public function getHost(): string {
        return htmlspecialchars($_SERVER['HTTP_HOST']);
    }
    
    public function getQueryParams(): array {
        return $_GET;
    }
    
    public function getReplacedHost(): string {
        return str_replace($this->getHost(), '', $this->getRefere());
    }
    
    public function getAdditionalParams(array $indexes): array {
        $indexes = [];
        return $indexes;
    }

    public function method(): string { 
        return strtolower($_SERVER['REQUEST_METHOD']) ?? 'get';
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
    
    public function getPHPInput() {
        return json_decode(file_get_contents('php://input'));
    }

}