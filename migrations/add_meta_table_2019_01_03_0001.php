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
            MetaID INT AUTO_INCREMENT PRIMARY KEY,
            EntityType VARCHAR(20) NOT NULL,
            EntityID int(10) NOT NULL,
            Data TEXT)";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Meta;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}