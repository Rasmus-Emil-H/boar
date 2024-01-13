<?php

namespace app\models;

use \app\core\src\database\Entity;

class TranslationModel extends Entity {

	const keyID     = 'TranslationID';
	const tableName = 'Translations';

	public function getAttributes(): array {
		return ['translation'];
	}

	public function getTableName(): string {
		return 'Translations';
	}
	
	public function getKeyField(): string {
		return 'TranslationID';
	}

}