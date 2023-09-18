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

	const keyID     = 'LanguageID';
	const tableName = 'Languages';

	public function getAttributes(): array {
		return ['language'];
	}
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'Languages';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'LanguageID';
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