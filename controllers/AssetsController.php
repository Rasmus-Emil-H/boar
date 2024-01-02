<?php

/**
 * Client asset Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/  

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class AssetsController {

    private string $objectID = 'clientAssets';

    public function get(string $section): array {
        return CoreFunctions::app()->getConfig()->get($this->objectID)->$section;
    }

}