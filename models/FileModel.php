<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\database\table\Table;
use \app\core\src\File;
use \app\core\src\miscellaneous\CoreFunctions;
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
		$path = $this->get('Path');

		return new File(
			CoreFunctions::last(explode(DIRECTORY_SEPARATOR, $path))->scalar, 
			dirname($path)
		);
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

		$b64 = 'data:image/' . $arguments->file->getFileType() . ';base64,' . base64_encode(file_get_contents($arguments->destination));

		return ['b64' => $b64, 'id' => $this->key(), 'created' => date('d-m-y H:i', strtotime('now'))];
	}

}