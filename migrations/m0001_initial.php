<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

class m0001_initial {

    public function up() {
        $database = app\core\Application::$app->database;
        $SQL = "CREATE TABLE IF NOT EXISTS Users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            firstname VARCHAR(255) NOT NULL,
            lastname VARCHAR(255) NOT NULL,
            status TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        $database->pdo->exec($SQL);
    }

    public function down() {
        $database = app\core\Application::$app->database;
        $SQL = "DROP TABLE users;";
        $database->pdo->exec($SQL);
    }

}