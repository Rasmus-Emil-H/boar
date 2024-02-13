<?php

namespace app\models;

use \app\core\src\database\Entity;

final class CronModel extends Entity {
	
	public function getTableName(): string {
		return 'Cronjob';
	}
		
	public function getKeyField(): string {
		return 'CronjobID';
	}
	
}