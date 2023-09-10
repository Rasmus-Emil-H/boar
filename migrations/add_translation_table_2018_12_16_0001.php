<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_translation_table_2018_12_16_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Translations (
            TranlationID INT AUTO_INCREMENT PRIMARY KEY,
            Translation VARCHAR(50) NOT NULL,
            LanguageID int(5) NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (LanguageID) REFERENCES Languages(LanguageID)
        )";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Translations;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}