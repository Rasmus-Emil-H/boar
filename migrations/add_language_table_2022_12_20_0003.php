<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

class add_language_table_2022_12_20_0003 {

    public function up() {
        $database = app\core\Application::$app->database;
        $SQL = "CREATE TABLE IF NOT EXISTS Languages (
            LanguageID INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        $database->pdo->exec($SQL);
    }

    public function down() {
        $database = app\core\Application::$app->database;
        $SQL = "DROP TABLE Languages;";
        $database->pdo->exec($SQL);
    }

}