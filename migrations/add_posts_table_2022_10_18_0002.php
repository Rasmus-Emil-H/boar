<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_posts_table_2022_10_18_0002 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Posts (
            PostID INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(50) NOT NULL,
            body TEXT NOT NULL,
            UserID INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        Application::$app->connection->exec($SQL);
    }

    public function down() {
        $SQL = "DROP TABLE Users;";
        Application::$app->connection->exec($SQL);
    }

}