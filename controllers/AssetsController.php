<?php

/**
 * Client asset Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/  

namespace app\controllers;

use \app\core\Controller;

class AssetsController extends Controller {

    private object $assets;
    private string $objectID = 'clientAssets';

    public function __construct() {
        $this->assets = app()->getConfig()->get($this->objectID);
    }

    public function get(string $section): array {
        return $this->assets->$section;
    }

}