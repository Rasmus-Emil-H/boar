<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;
use \app\core\src\database\seeders\DatabaseSeeder;

class add_roles_table_2017_12_15_0001 {

    private const DEFAULT_ROLES = ['User', 'Admin'];

    public function up() {
        (new Schema())->up('Roles', function(Table $table) {
            $table->increments('RoleID');
            $table->varchar('Name', 20);
            $table->timestamp();
            $table->primaryKey('RoleID');
        });
    }

    public function down() {
        (new Schema())->down('Roles');
    }

}