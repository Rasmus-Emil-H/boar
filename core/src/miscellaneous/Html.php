<?php

namespace app\core\src\miscellaneous;

class Html {

    public static function escape(string $input): string {
        return htmlspecialchars($input);
    }

}