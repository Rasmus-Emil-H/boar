<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_users_table_2018_12_14_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Users (
            UserID INT AUTO_INCREMENT PRIMARY KEY,
            Email VARCHAR(255) NOT NULL,
            Name VARCHAR(255) NOT NULL,
            Status INT(1) DEFAULT 0,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Users;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}