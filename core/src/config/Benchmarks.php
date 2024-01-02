<?php

/**
 * Server benchmarks
 * AUTHOR: RE_WEB
 * @package app\core\src\config
 */

namespace app\core\src\config;

use app\core\src\miscellaneous\CoreFunctions;

class Benchmarks {

    public static function determineServerHashCost(float $timeTarget): void {
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_DEFAULT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        CoreFunctions::dd(__FUNCTION__ . ' RAN with result: [ Appropriate Cost Found: ' . $cost . ' ]');
    }

}