<?php

namespace app\utilities;

class Utilities {

    public static function appendToStringIfKeyNotLast(array $arrayKey, string|int $iterationKey, string $appender = ', '): null|string {
        return array_key_last($arrayKey) === $iterationKey ? null : $appender;
    }

}