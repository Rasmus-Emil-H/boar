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

    protected function validateRoute(): string {
        return '';
    }

}