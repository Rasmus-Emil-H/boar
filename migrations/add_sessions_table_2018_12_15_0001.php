<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_sessions_table_2018_12_15_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Sessions (
            SessionID INT AUTO_INCREMENT PRIMARY KEY,
            Value VARCHAR(50) NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Sessions;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}