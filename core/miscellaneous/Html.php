<?php

namespace app\core\miscellaneous;

class Html {

    public static function escape(string $input): string {
        return htmlspecialchars($input);
    }

}