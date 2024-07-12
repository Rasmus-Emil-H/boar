<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_Test_table_2024_07_12_0001 {
    public function up() {
        (new Schema())->up('Test', function(Table $table) {
            $table->increments('YourID');
            $table->timestamp();
            $table->primaryKey('YourID');
        });
    }

    public function down() {
        (new Schema())->down('Test');
    }
}