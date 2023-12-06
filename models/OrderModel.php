<?php

/**
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
 */

namespace app\models;

use \app\core\database\Entity;

class OrderModel extends Entity {

    const keyID     = 'OrderID';
	const tableName = 'Orders';
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'Orders';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'OrderID';
	}

	public function getForeignKeys(): array {
		return [];
	}

    public function user() {
        return $this->belongsTo(UserModel::class);
    }
	
}