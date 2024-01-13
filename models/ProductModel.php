<?php

namespace app\models;

use \app\core\src\database\Entity;

class ProductModel extends Entity {

    const keyID     = 'ProductID';
	const tableName = 'Products';

	public function getTableName(): string {
		return 'Products';
	}
	
	public function getKeyField(): string {
		return 'ProductID';
	}

    public function user() {
        return $this->belongsTo(OrderModel::class);
    }
	
}