<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;
use \app\core\src\database\seeders\DatabaseSeeder;

class add_roles_table_2018_12_15_0001 {

    private object $DEFAULT_ROLES;

    public function __constructor() {
        $this->DEFAULT_ROLES = (object)['User', 'Sysadmin'];
    }

    public function up() {
        (new Schema())->up('Roles', function(Table $table) {
            $table->increments('RoleID');
            $table->varchar('Name', 50);
            $table->timestamp();
            $table->primaryKey('RoleID');
        });
        
        foreach ($this->DEFAULT_ROLES as $role)
            (new DatabaseSeeder())->up('Roles', ['Name' => $role->name], 1);
    }

    public function down() {
        (new Schema())->down('Roles');
    }

}