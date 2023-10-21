<?php

class edit_meta_table_add_timestamp_2019_12_12_0001 {

    public function up() {
        $SQL = "ALTER TABLE Meta ADD COLUMN CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "ALTER TABLE Meta DROP COLUMN CreatedAt;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}