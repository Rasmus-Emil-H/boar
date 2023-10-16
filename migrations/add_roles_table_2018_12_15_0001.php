<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_roles_table_2018_12_15_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Roles (
            RoleID INT AUTO_INCREMENT PRIMARY KEY,
            Name VARCHAR(50) NOT NULL
        )";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Roles;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}