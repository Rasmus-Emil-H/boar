<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_role_permission_table_2020_01_02_0001 {

    public function up() {
        (new Schema())->up('role_permission', function(Table $table) {
            $table->increments('PivotID');
            $table->integer('RoleID', 10);
            $table->integer('PermissionID', 10);
            $table->timestamp();
            $table->primaryKey('PivotID');
            $table->foreignKey('RoleID', 'Roles', 'RoleID');
            $table->foreignKey('PermissionID', 'Permissions', 'PermissionID');
        });
    }

    public function down() {
        (new Schema())->down('role_permission');
    }

}