<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class edit_user_table_add_password_2018_12_14_0001 {

    public function up() {
        $SQL = "ALTER TABLE Users ADD COLUMN Password VARCHAR(255) ";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Users DROP COLUMN Password;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}