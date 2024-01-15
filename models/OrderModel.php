<?php

namespace app\models;

use \app\core\src\database\Entity;

final class OrderModel extends Entity {
	
	public function getTableName(): string {
		return 'Orders';
	}
	
	public function getKeyField(): string {
		return 'OrderID';
	}

    public function user() {
        return $this->belongsTo(UserModel::class);
    }
	
}