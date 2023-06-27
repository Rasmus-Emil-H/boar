<?php

namespace app\config;

class Benchmarks {

    /**
     * This code will benchmark your server to determine how high of a cost you can
     * afford. You want to set the highest cost that you can without slowing down
     * you server too much. determineCost aims for ≤ $timeTarget ( etc 0.05 = 50 milliseconds ) stretching time,
     * which is a good baseline for systems handling interactive logins.  
     * @var float $timeTarget
     * @return void
    */

    public static function determineCost(float $timeTarget): void {
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        exit("Appropriate Cost Found: $cost");
    }

}