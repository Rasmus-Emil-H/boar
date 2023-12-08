<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_roles_table_2018_12_15_0001 {

    public function up() {
        (new Schema())->up('Roles', function(Table $table) {
            $table->increments('RoleID');
            $table->varchar('name', 50);
            $table->timestamp();
            $table->primaryKey('RoleID');
        });
    }

    public function down() {
        (new Schema())->down('Roles');
    }

}