<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_products_table_2020_12_20_0001 {

    public function up() {
        (new Schema())->up('Products', function(Table $table) {
            $table->increments('ProductID');
            $table->varchar('ProductName', 50);
            $table->text('ProductDescription');
            $table->timestamp();
            $table->primaryKey('ProductID');
        });
    }

    public function down() {
        (new Schema())->down('Products');
    }

}