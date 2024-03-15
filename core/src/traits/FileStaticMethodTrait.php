<?php

namespace app\core\src\traits;

trait FileStaticMethodTrait {

    public static function base64Encode(string $filePath) {
        if (!file_exists($filePath)) 
            throw new \app\core\src\exceptions\NotFoundException(self::NO_FILES_ATTACHED);

        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($filePath));
    }

}