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
        $SQL = "CREATE TABLE IF NOT EXISTS Roles (
            RoleID INT AUTO_INCREMENT PRIMARY KEY,
            Name VARCHAR(50) NOT NULL
        )";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Roles;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}