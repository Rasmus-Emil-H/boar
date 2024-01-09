<?php

namespace app\models;

use \app\core\src\database\Entity;

class OrderModel extends Entity {

    const keyID     = 'OrderID';
	const tableName = 'Orders';

	public function getTableName(): string {
		return 'Orders';
	}
	
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