<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

class m0002_add_password_to_user_table {

    public function up() {
        $database = app\core\Application::$app->database;
        $SQL = "ALTER TABLE Users ADD COLUMN password VARCHAR (255) ";
        $database->pdo->exec($SQL);
    }

    public function down() {
        $database = app\core\Application::$app->database;
        $SQL = "DROP TABLE users;";
        $database->pdo->exec($SQL);
    }

}