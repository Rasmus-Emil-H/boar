<?php

/**
 * Translation
 * AUTHOR: RE_WEB
 * @package app\core\i18n
*/

namespace app\core;

use \app\models\LanguageModel;
use \app\models\TranslationModel;

#[\AllowDynamicProperties]

class I18n {

    protected string $currentLanguage;
    protected int $languageID;

    public function __construct() {
        $this->currentlanguage = app()->session->get('language');
        $this->languageID = 1;
    }

    public function translate(string $toTranslate): string {
        return TranslationModel::search(['LanguageID' => $this->languageID, 'TranslationKey' => $toTranslate]) ?? $this->registerMissingTranslation($toTranslate);
    }

    public function registerMissingTranslation(string $missingTranslation): string {
        foreach ( LanguageModel::all() as $language ) {
            $translation = new TranslationModel();
            $translation
                ->set(['translation' => $missingTranslation, 'languageID' => $language->key()])
                ->save();
        }
    }
}