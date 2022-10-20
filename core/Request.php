<?php

/*******************************
 * Bootstrap Request 
 * AUTHOR: RE_WEB
 * @package app\core\Request
*/

namespace app\core;

class Request {

    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if(!$position) return $path;
        return substr($path, 0, $position);
    }

    public function method() {
        return strtolower($_SERVER['REQUEST_METHOD']) ?? 'get';
    }

    public function isGet() {
        return $this->method() === 'get';
    }

    public function isPost() {
        return $this->method() === 'post';
    }

    public function getBody() {
        
        $body = [];

        if ($this->method() === 'get')
            foreach ($_GET as $key => $value)
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->method() === 'post') 
            foreach ($_POST as $key => $value)
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);

        return $body;
    }

}