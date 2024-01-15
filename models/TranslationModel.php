<?php

namespace app\models;

use \app\core\src\database\Entity;

final class TranslationModel extends Entity {

	public function getTableName(): string {
		return 'Translations';
	}
	
	public function getKeyField(): string {
		return 'TranslationID';
	}

}