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
        $language = LanguageModel::query()->select()->where(['code' => strtolower(app()->session->get('language'))])->run();
        if (!$language) throw new NotFoundException("Language was not found");
        $this->languageID = $language[array_key_first((array)$language)]->key();
    }

    public function translate(string $toTranslate): string {
        $translationExists = TranslationModel::query()->select()->where(['LanguageID' => $this->languageID, 'Translation' => $toTranslate])->run();
        if ($translationExists) return first($translationExists)->get('Translation');
        $this->registerMissingTranslation($toTranslate);
        return $toTranslate;
    }

    public function registerMissingTranslation(string $missingTranslation) {
        (new TranslationModel())
            ->set(['Translation' => $missingTranslation, 'LanguageID' => $this->languageID, 'TranslationHash' => Hash::create()])
            ->save();
    }
    
}