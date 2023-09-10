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
        return LanguageModel::search(['languageID' => $this->languageID, 'translationKey' => $toTranslate]) ?? $this->registerMissingTranslation($toTranslate);
    }

    public function registerMissingTranslation(string $missingTranslation): string {
        foreach ( $this->languages() as $language ) {
            $translation = new TranslationModel();
            $translation->set(['translation' => $missingTranslation, 'languageID' => $language->key()]);
        }
    }
}