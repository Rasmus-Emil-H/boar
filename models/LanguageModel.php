<?php

/**
 * Bootstrap language model
 * AUTHOR: RE_WEB
 * @package app\models\Query
 */

namespace app\models;

use \app\core\src\database\Entity;

class LanguageModel extends Entity {

	const keyID     = 'LanguageID';
	const tableName = 'Languages';
	
	public function getTableName(): string {
		return 'Languages';
	}
	
	public function getKeyField(): string {
		return 'LanguageID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
	public function rules(): array {
		return [];
	}

}