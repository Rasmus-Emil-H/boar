<?php

namespace app\core\src\utilities;

use app\core\src\miscellaneous\CoreFunctions;

class Parser {
    
    public static function sqlComparsion(string $sqlInstruction, array $validComparisonOperators): array {
        $valueParts = explode(' ', $sqlInstruction);
        if (count($valueParts) > 1 && in_array((CoreFunctions::first($valueParts)->scalar), $validComparisonOperators)) 
            return [CoreFunctions::first($valueParts)->scalar, CoreFunctions::getIndex($valueParts, 1)->scalar];
        return ['=', $sqlInstruction];
    }

    public static function xml($response) {
        $xml = simplexml_load_string($response);
        return $xml;
    }

}