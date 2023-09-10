<?php

/**
 * Translation
 * AUTHOR: RE_WEB
 * @package app\core\i18n
*/

namespace app\core;

use \app\models\LanguageModel;

#[\AllowDynamicProperties]

class I18n {

    public function __construct() {
        $this->currentLanguage = Application::$app->session->get('language');
        $this->languages = LanguageModel::all();
    }

    public function translate(string $toTranslate): string {
        return Application::$app->connection->select("t_translations t", ["t.translation"])->where(["t.languageID" => $this->languageID, "t.translationKey" => $toTranslate])->execute()[0]['translation'] ?? $this->registerMissingTranslation($toTranslate);
    }

    public function registerMissingTranslation(string $missingTranslation): string {
        // foreach ( $this->get() as $language ) {
        //     var_dump($language);
        // }
        return 'Missing translation';
    }
}