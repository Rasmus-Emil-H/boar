<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_users_table_2015_12_13_0001 {

    public function up() {
        (new Schema())->up('Users', function(Table $table) {
            $table->increments('UserID');
            $table->varchar('Email', 100);
            $table->varchar('Name', 50);
            $table->integer('Status', 1);
            $table->timestamp();
            $table->primaryKey('UserID');
        });
    }

    public function down() {
        (new Schema())->down('Users');
    }

}