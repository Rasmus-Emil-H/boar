<?php

namespace app\utilities;

class Parser {
    
    public static function sqlComparsion(string $sqlInstruction, array $validComparisonOperators): array {
        $valueParts = explode(' ', $sqlInstruction);
        if (count($valueParts) > 1 && in_array((first($valueParts)->scalar), $validComparisonOperators)) return [first($valueParts)->scalar, getIndex($valueParts, 1)->scalar];
        return ['=', $sqlInstruction];
    }

}