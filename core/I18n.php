<?php

/**
 * Translation
 * AUTHOR: RE_WEB
 * @package app\core\i18n
 */

namespace app\core;

use app\core\exceptions\NotFoundException;

use \app\models\LanguageModel;
use \app\models\TranslationModel;
use \app\core\miscellaneous\Hash;

class I18n {

    protected int $languageID;

    public function __construct() {
        $language = LanguageModel::search(['name' => app()->session->get('language')]);
        if (!$language) throw new NotFoundException("Language was not found");
        $this->languageID = $language[array_key_first((array)$language)]->key();
    }

    public function translate(string $toTranslate): string {
        $translationExists = TranslationModel::search(['LanguageID' => $this->languageID, 'Translation' => $toTranslate]);
        if (!$translationExists) {
            $this->registerMissingTranslation($toTranslate);
            return $toTranslate;
        }
        else return $translationExists[array_key_first((array)$translationExists)]->get('Translation');
    }

    public function registerMissingTranslation(string $missingTranslation) {
        (new TranslationModel())
            ->set(['Translation' => $missingTranslation, 'LanguageID' => $this->languageID, 'TranslationHash' => Hash::create()])
            ->save();
    }
    
}