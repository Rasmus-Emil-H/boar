<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_translations_table_2018_12_16_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS Translations (
            TranslationID INT AUTO_INCREMENT PRIMARY KEY,
            Translation VARCHAR(50) NOT NULL,
            LanguageID int(5) NOT NULL,
            TranslationHash VARCHAR(50) NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (LanguageID) REFERENCES Languages(LanguageID)
        )";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Translations;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}