<?php

/**
|----------------------------------------------------------------------------
| Third party communication base
|----------------------------------------------------------------------------
|
| Base for communicating with external services
|
| @author RE_WEB
| @package app\core\src\thirdpartycommunication
|
*/

namespace app\core\src\thirdpartycommunication;

use \app\core\src\Curl;

abstract class ThirdPartyCommunication {

    public function __construct(
        protected array $arguments = [],
        protected Curl $curl = new Curl()
    ) {
    }
    
    abstract public function send();
    abstract public function receive();

    protected function getArguments(): array {
        return $this->arguments;
    }

}