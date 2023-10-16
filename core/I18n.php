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
        $translationExists = TranslationModel::search(['LanguageID' => $this->languageID, 'Translation' => $toTranslate]);
        if (!$translationExists) {
            $this->registerMissingTranslation($toTranslate);
            return $toTranslate;
        }
        else return $translationExists[array_key_first($translationExists)]->get('Translation');
    }

    public function registerMissingTranslation(string $missingTranslation) {
        $translation = new TranslationModel();
        $translation
            ->set(['Translation' => $missingTranslation, 'LanguageID' => $this->languageID, 'TranslationHash' => \app\core\miscellaneous\Hash::create()])
            ->save();
    }
}