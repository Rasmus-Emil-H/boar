<?php

namespace app\core\factories;

interface FactoryInterface {

    public function __construct(array $arguments = []);
    public function create();

}