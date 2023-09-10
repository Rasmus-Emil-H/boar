<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class edit_meta_table_add_timestamp_2019_12_12_0001 {

    public function up() {
        $SQL = "ALTER TABLE Meta ADD COLUMN CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Meta DROP COLUMN CreatedAt;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}