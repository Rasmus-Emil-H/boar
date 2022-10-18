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

    public function strip(string $string, array $characters = []): string {
        return preg_replace('/'.implode($characters).'/', '', $this->string);
    }

    public function validateRoute(): string {
        return $this->strip($this->string, ['\/']);
    }

}