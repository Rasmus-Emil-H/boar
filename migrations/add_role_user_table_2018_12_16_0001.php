<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_role_user_table_2018_12_16_0001 {

    public function up() {
        (new Schema())->up('role_user', function(Table $table) {
            $table->increments('PivotID');
            $table->integer('UserID', 10);
            $table->integer('RoleID', 2);
            $table->timestamp();
            $table->primaryKey('PivotID');
        });
    }

    public function down() {
        (new Schema())->down('role_user');
    }

}