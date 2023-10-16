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
            Title VARCHAR(50) NOT NULL,
            Body TEXT NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Posts;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}