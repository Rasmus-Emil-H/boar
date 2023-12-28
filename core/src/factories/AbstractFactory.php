<?php

namespace app\core\src\factories;

abstract class AbstractFactory {

    public function __construct(
        protected array $arguments = []
    ) {

    }
    public function create() {
        
    }

}