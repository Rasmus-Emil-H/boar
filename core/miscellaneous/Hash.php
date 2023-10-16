<?php

namespace app\core\miscellaneous;

class Hash {

    public static function create(int $length = 50): string {
        $randomBytes  = random_bytes($length);
        $uniqueString = base64_encode($randomBytes);
        $uniqueString = substr(preg_replace("/[^a-zA-Z0-9]/", "", $uniqueString), 0, $length);
        return $uniqueString;
    }

}