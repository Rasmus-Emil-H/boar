<?php

namespace app\controllers;

use \app\core\src\miscellaneous\CoreFunctions;

class AssetsController {
    
    public function get(string $section): array {
        return app()->getConfig()->get('clientAssets')->$section;
    }

}