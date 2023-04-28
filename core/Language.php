<?php

namespace app\core;

class Language extends DbModel {

    protected string $currentLanguage;

    public function __construct() {
        $this->currentLanguage = Application::$app->session->get('language');
    }

    public function translation(string $toTranslate): string {
        return 123;
    }
}