<?php

namespace app\controllers;

class AssetsController {
    
    public function get(string $section): array {
        return app()->getConfig()->get('clientAssets')->$section;
    }

    public function set(string $section, string $path): void {
        app()->getConfig()->get('clientAssets')->$section[] = $path;
    }

}