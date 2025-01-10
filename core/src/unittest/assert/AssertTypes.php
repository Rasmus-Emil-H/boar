<?php

namespace app\core\src\unittest\assert;

trait AssertTypes {
    public function assertInstanceOf() {

    }

    public function assertIsArray() {

    }

    public function assertIsList() {

    }

    public function assertIsBool($value): bool {
        return gettype($value) === 'boolean';
    }

    public function assertIsCallable() {

    }

    public function assertIsFloat() {

    }

    public function assertIsInt() {

    }

    public function assertIsIterable() {

    }

    public function assertIsNumeric() {

    }

    public function assertIsObject() {

    }

    public function assertIsResource() {

    }

    public function assertIsScalar() {

    }

    public function assertIsString() {

    }

    public function assertNull() {

    }
}