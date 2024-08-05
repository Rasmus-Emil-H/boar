<?php

namespace app\models;

use \app\core\src\database\Entity;

final class CommentModel extends Entity {

	public function getTableName(): string {
		return 'Comments';
	}
		
	public function getKeyField(): string {
		return 'CommentID';
	}
	
}