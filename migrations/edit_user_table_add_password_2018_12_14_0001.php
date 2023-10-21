<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class edit_user_table_add_password_2018_12_14_0001 {

    public function up() {
        $SQL = "ALTER TABLE Users ADD COLUMN Password VARCHAR(255) ";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Users DROP COLUMN Password;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}