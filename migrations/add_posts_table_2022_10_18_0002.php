<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

class add_posts_table_2022_10_18_0002 {

    public function up() {
        $database = app\core\Application::$app->database;
        $SQL = "CREATE TABLE IF NOT EXISTS Posts (
            PostID INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(50) NOT NULL,
            body TEXT NOT NULL,
            UserID INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        $database->pdo->exec($SQL);
    }

    public function down() {
        $database = app\core\Application::$app->database;
        $SQL = "DROP TABLE Users;";
        $database->pdo->exec($SQL);
    }

}