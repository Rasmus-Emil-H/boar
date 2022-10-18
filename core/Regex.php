<?php

/*******************************
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\core\Regex
 * Global regex handler
*/

namespace app\core;

class Regex {

    protected string $string;

    public function __construct(string $string) {
        $this->string = $string;
    }

    public function replace(string $string, array $characters = []): string {
        return preg_replace('/'.implode($characters).'/', '', $this->string);
    }

    public static function match(string $string): bool {
        return preg_match('/a-zA-Z0-9/', $string);
    }

    public function validateRoute(): array {
        return explode('/', $this->string);
    }

}