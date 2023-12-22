<?php

/**
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\core\Regex
 * Global regex handler
 */

namespace app\core\src;

class Regex {

    protected string $string;
    protected array  $excludes = ['', 'dev'];

    public function __construct(string $string) {
        $this->string = $string;
    }

    public function replace(string $string, string $with, array $characters = []): string {
        return preg_replace('/'.implode($characters).'/', $with, $string);
    }

    public static function match(string $pattern, string $string): bool {
        return preg_match($pattern, $string);
    }

    public function validateRoute(): array {
        $includes = [];
        foreach (explode('/', $this->string) as $param)
            if (!in_array($param, $this->excludes)) $includes[] = $param;
        return $includes;
    }

}