<?php

namespace app\models;

use \app\core\src\database\Entity;

class SystemEventModel extends Entity {
	
	public function getTableName(): string {
		return 'SystemEvents';
	}
	
	public function getKeyField(): string {
		return 'SystemEventID';
	}

}