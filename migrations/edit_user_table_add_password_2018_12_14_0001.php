<?php

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