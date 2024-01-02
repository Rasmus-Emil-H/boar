<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_permissions_table_2020_01_02_0001 {

    public function up() {
        (new Schema())->up('Permissions', function(Table $table) {
            $table->increments('PermissionID');
            $table->varchar('Name', 50);
            $table->varchar('Readable', 100);
            $table->timestamp();
            $table->primaryKey('PermissionID');
        });
    }

    public function down() {
        (new Schema())->down('Permissions');
    }

}