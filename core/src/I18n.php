<?php

/**
|----------------------------------------------------------------------------
| Bootstrap Translations
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package app\core\src
|
*/

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;
use \app\models\LanguageModel;
use \app\models\TranslationModel;
use \app\core\src\miscellaneous\Hash;
use \app\core\src\miscellaneous\CoreFunctions;

final class I18n {

    protected int $languageID;

    public function __construct() {
        if (IS_CLI) return;

        $language = (new LanguageModel())->query()->select()->where(['code' => strtolower(app()->getSession()->get('language'))])->run();
        if (!$language) throw new NotFoundException("Language was not found");
        $this->languageID = CoreFunctions::first($language)->key();
    }

    public function translate(string $toTranslate): string {
        $translationExists = (new TranslationModel())->query()->select()->where(['LanguageID' => $this->languageID, 'Translation' => $toTranslate])->run();
        if ($translationExists) return CoreFunctions::first($translationExists)->get('TranslationHumanReadable');
        $this->registerMissingTranslation($toTranslate);
        return $toTranslate;
    }

    public function registerMissingTranslation(string $missingTranslation) {
        (new TranslationModel())
            ->set(['Translation' => $missingTranslation, 'TranslationHumanReadable' => $missingTranslation, 'LanguageID' => $this->languageID, 'TranslationHash' => Hash::create()])
            ->save();
    }

    public function getCurrentLanguageObject() {
        return new LanguageModel($this->languageID);
    }
    
}