<?php

namespace app\models;

use \app\core\src\database\Entity;

final class LanguageModel extends Entity {

	public function getTableName(): string {
		return 'Languages';
	}
	
	public function getKeyField(): string {
		return 'LanguageID';
	}

	/**
	  |----------------------------------------------------------------------------
	  | ENTITY RELATIONS FOR __CLASS__
	  |----------------------------------------------------------------------------
	  |
	  |
	 */

	public function translations() {
		return $this->hasMany(TranslationModel::class)->run();
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

	private const ALLOWED_HTTP_METHODS = [
		'getTranslations', 'create', 'remove'
	];

	public function setAllowedHTTPMethods() {
		$this->setValidHTTPMethods(self::ALLOWED_HTTP_METHODS);
	}

	public function getTranslations() {
		$frontend = [];
		
		foreach ($this->translations() as $translation)
			$frontend[hs($translation->key())] = [hs($translation->get('Translation')) => hs($translation->get('TranslationHumanReadable'))];

		return $frontend;
	}

	public function create(object $arguments) {

		$cLanguage = new $this();
		$cLanguage->set(['Name' => $arguments->name, 'code' => strtolower($arguments->name)]);
		$cLanguage->save();

		return [
			'message' => 'Language added',
			'name' => $cLanguage->get('Name'),
			'id' => $cLanguage->key()
		];
	}

	public function remove() {
		if ($this->key() === '1') return hs('Default language can\'t be deleted.');

		$this->delete();
		return hs('Language deleted');
	}

}
