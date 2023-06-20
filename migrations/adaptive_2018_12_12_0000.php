<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class adaptive_2018_12_12_0000 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Users (
            UserID INT AUTO_INCREMENT PRIMARY KEY,
            Email VARCHAR(255) NOT NULL,
            Firstname VARCHAR(255) NOT NULL,
            Lastname VARCHAR(255) NOT NULL,
            Status TINYINT DEFAULT 0,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Users;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}