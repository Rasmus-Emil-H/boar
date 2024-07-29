<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\database\table\Table;
use \app\core\src\File;
use \app\core\src\miscellaneous\Hash;

final class FileModel extends Entity {
	
	public function getTableName(): string {
		return 'Files';
	}
	
	public function getKeyField(): string {
		return 'FileID';
	}

	public function getPivot(): string {
		return 'file_entity';
	}

	public function equivalentFile(): File {
		return new File($this->get('Path'));
	}

	/**
	  |----------------------------------------------------------------------------
	  | HTTP METHODS
	  |----------------------------------------------------------------------------
	  | Below are methods that the client can interact with
	  | from their respective models
	  | 
	  | The methods below should be used via controllers
	  |
	 */

	protected array $ALLOWED_HTTP_METHODS = [
		'attachFile', 'delete'
	];

	public function attachFile(object $arguments) {
		$this->setData([
			'Name' => $arguments->file->getName(),
			'Path' => $arguments->destination,
			'Hash' => Hash::file($arguments->destination),
			'Type' => $arguments->customFileType
		]);

		$this
			->save()
			->createPivot([
				Table::ENTITY_TYPE_COLUMN => $arguments->body->entityType, 
				Table::ENTITY_ID_COLUMN => $arguments->body->entityID, 
				$this->getKeyField() => $this->key()
			]);

		$path = file_get_contents($arguments->destination);
		$b64 = 'data:image/' . $arguments->file->getFileType() . ';base64,' . base64_encode($path);

		return ['b64' => $b64, 'id' => $this->key(), 'created' => date('d-m-y H:i', strtotime('now'))];
	}

}