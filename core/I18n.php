<?php

/*******************************
 * Translation
 * AUTHOR: RE_WEB
 * @package app\core\i18n
*******************************/

namespace app\core;

class I18n {

    public function __construct() {
        $this->currentLanguage = Application::$app->session->get('language');
        $this->languages = \app\models\LanguageModel::all();
        echo '<pre>';var_dump($this->languages);echo '</pre>';
        // $this->languageID = Application::$app->connection->select("t_languages l", ["l.languageID"])->where(["l.language" => $this->currentLanguage])->execute($this)[0]['languageID']??0;
        // $this->languageID = $this->languageID;
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