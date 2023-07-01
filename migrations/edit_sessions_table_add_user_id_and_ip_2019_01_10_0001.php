<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class edit_sessions_table_add_user_id_and_ip_2019_01_10_0001 {

    public function up() {
        $SQL = "ALTER TABLE Sessions
            ADD COLUMN UserID INT(10) NOT NULL,
            ADD COLUMN IP VARCHAR(30) NOT NULL
        ";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Sessions
            REMOVE COLUMN UserID,
            REMOVE COLUMN IP
        ;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}