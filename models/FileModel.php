<?php

namespace app\models;

use app\core\src\database\Entity;

class FileModel extends Entity {

    const keyID     = 'FileID';
	const tableName = 'Files';
	
	public function getTableName(): string {
		return 'Files';
	}
	
	public function getKeyField(): string {
		return 'FileID';
	}

}