<?php

namespace app\core\src\factories;

interface FactoryInterface {

    public function __construct(array $arguments = []);
    public function create();

}