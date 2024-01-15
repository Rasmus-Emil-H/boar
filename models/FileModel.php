<?php

namespace app\models;

use app\core\src\database\Entity;

final class FileModel extends Entity {
	
	public function getTableName(): string {
		return 'Files';
	}
	
	public function getKeyField(): string {
		return 'FileID';
	}

}