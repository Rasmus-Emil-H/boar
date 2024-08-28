<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_state_table_2024_08_27_0001 {

    public function up() {
        (new Schema())->up('States', function(Table $table) {
            $table->increments('StateID');
            $table->varchar('Name', 20);
            $table->timestamp();
            $table->primaryKey('StateID');
        });
    }

    public function down() {
        (new Schema())->down('States');
    }

}