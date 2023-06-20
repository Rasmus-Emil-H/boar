<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_meta_table_2019_01_03_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Meta (
            MetaId INT AUTO_INCREMENT PRIMARY KEY,
            EntityType VARCHAR(255) NOT NULL,
            EntityID VARCHAR(255) NOT NULL,
            Data INT(1) DEFAULT 0,
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