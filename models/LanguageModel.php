<?php

/*******************************
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
*******************************/

namespace app\models;

use \app\core\database\Entity;
use \app\core\Application;

class LanguageModel extends Entity {

	const keyID     = 'languageID';
	const tableName = 't_languages';

	public function getAttributes(): array {
		return ['language'];
	}
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 't_languages';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'languageID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
	public function rules(): array {
		return [];
	}

	public function getRelationObjects() {
		return ['Translation'];
	}

}