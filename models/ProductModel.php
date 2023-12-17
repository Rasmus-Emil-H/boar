<?php

/**
 * Bootstrap Product model 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
 */

namespace app\models;

use \app\core\database\Entity;

class ProductModel extends Entity {

    const keyID     = 'ProductID';
	const tableName = 'Products';

	public function getTableName(): string {
		return 'Products';
	}
	
	public function getKeyField(): string {
		return 'ProductID';
	}

	public function getForeignKeys(): array {
		return [];
	}

    public function user() {
        return $this->belongsTo(UserModel::class);
    }
	
}