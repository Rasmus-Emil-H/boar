<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class edit_sessions_table_add_user_id_and_ip_2019_01_10_0001 {

    public function up() {
        $SQL = "ALTER TABLE Sessions
            ADD COLUMN UserID INT(10) NOT NULL,
            ADD COLUMN IP VARCHAR(30) NOT NULL
        ";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Sessions
            REMOVE COLUMN UserID,
            REMOVE COLUMN IP
        ;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}