<?php

/*******************************
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
*******************************/

namespace app\models;

use \app\core\DbModel;
use \app\core\Application;

class LanguageModel extends DbModel {

	public string $name;
	public string $language;

	public function getLanguages(): array {
		return Application::$app->database
			->select('t_languages l', ['l.*'])
			->execute();
	}

	public function getAttributes(): array {
		return ['language'];
	}
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function tableName(): string {
		return 't_languages';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getPrimaryKey(): string {
		return 'languageID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
	public function rules(): array {
		return [];
	}

}