<?php

namespace app\core\src\traits;

trait FileStaticMethodTrait {

    public static function base64Encode(string $filePath) {
        if (!file_exists($filePath)) return 'data:image/jpeg;base64,';

        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($filePath));
    }

}