<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_sessions_table_2017_12_15_0001 {

    public function up() {
        (new Schema())->up('Sessions', function(Table $table) {
            $table->increments('SessionID');
            $table->varchar('Value', 100);
            $table->timestamp();
            $table->integer('UserID', 10);
            $table->varchar('IP', 30);
            $table->primaryKey('SessionID');
            $table->foreignKey('UserID', 'Users');
        });
    }

    public function down() {
        (new Schema())->down('Sessions');
    }

}