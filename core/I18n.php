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

    protected string $currentLanguage;
    protected int $languageID;

    public function __construct() {
        $this->currentlanguage = app()->session->get('language');
        $this->languageID = 1;
        $this->languages = LanguageModel::all();
    }

    public function translate(string $toTranslate): string {
        return LanguageModel::search(['LanguageID' => $this->languageID, 'TranslationKey' => $toTranslate]) ?? $this->registerMissingTranslation($toTranslate);
    }

    public function registerMissingTranslation(string $missingTranslation): string {
        foreach ( $this->languages() as $language ) {
            $translation = new TranslationModel();
            $translation
                ->set(['translation' => $missingTranslation, 'languageID' => $language->key()])
                ->save();
        }
    }
}