<?php

namespace app\models;

use \app\core\src\database\Entity;

final class StateModel extends Entity {
	
	public function getTableName(): string {
		return 'States';
	}
	
	public function getKeyField(): string {
		return 'StateID';
	}

	public function getPivot(): string {
		return 'state_entity';
	}

}