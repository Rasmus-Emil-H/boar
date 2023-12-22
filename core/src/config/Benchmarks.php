<?php

/**
 * Server benchmarks
 * AUTHOR: RE_WEB
 * @package app\core\src\config
 */

namespace app\core\src\config;

class Benchmarks {

    public static function determineServerHashCost(float $timeTarget): void {
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_DEFAULT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        exit("Appropriate Cost Found: $cost");
    }

}