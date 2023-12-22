<?php

namespace app\core\src\factories;

abstract class AbstractFactory {

    protected const CONTROLLER = 'Controller';

    public function __construct(
        protected array $arguments = []
        ) {

    }
    public function create() {
        
    }

}