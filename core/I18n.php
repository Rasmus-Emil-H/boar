<?php

namespace app\core;

class I18n {

    protected string $currentLanguage;
    protected string $languageID;

    public function __construct() {
        $this->currentLanguage = Application::$app->session->get('language');
        $this->languageID = Application::$app->database->select("t_languages l", ["l.languageID"])->where(["l.language" => $this->currentLanguage])->execute()[0]['languageID']??0;
        $this->languageID = $this->languageID;
    }

    public function translate(string $toTranslate): string {
        return Application::$app->database->select("t_translations t", ["t.translation"])->where(["t.languageID" => $this->languageID, "t.translationKey" => $toTranslate])->execute()[0]['translation']??'Mangler oversættelse';
    }
}