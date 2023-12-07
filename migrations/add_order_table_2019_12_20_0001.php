<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_order_table_2019_12_20_0001 {

    public function up() {
        (new Schema())->up('Orders', function(Table $table) {
            $table->increments('OrderID');
            $table->timestamp('CreatedAt');
            $table->integer('Total', 10);
            $table->primaryKey('OrderID');
            $table->integer('UserID', 10);
            $table->foreignKey('UserID', 'Users', 'UserID');
        });
    }

    public function down() {
        (new Schema())->down('Orders');
    }

}