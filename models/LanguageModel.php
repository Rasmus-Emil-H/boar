<?php

namespace app\models;

use \app\core\src\database\Entity;

class LanguageModel extends Entity {

	public function getTableName(): string {
		return 'Languages';
	}
	
	public function getKeyField(): string {
		return 'LanguageID';
	}

}