<?php

/*******************************
 * Translation
 * AUTHOR: RE_WEB
 * @package app\core\i18n
*******************************/

namespace app\core;

class I18n {

    private \app\models\LanguageModel $languages;

    public function __construct() {
        $this->languages = new \app\models\LanguageModel();
        $this->currentLanguage = Application::$app->session->get('language');
        $res = $this->languages->get();
        echo '<pre>';var_dump($res);echo '</pre>';exit;
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