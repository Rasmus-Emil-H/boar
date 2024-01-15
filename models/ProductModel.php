<?php

namespace app\models;

use \app\core\src\database\Entity;

final class ProductModel extends Entity {

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