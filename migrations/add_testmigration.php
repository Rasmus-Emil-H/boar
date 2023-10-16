<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/



class add_testmigration {

    public function up() {
        $static = new \app\core\database\Schema();
        $static->create('qwd', function($table) {
            $table->string('qwd');
        });
        exit;
    }

    public function down() {
        $SQL = "DROP TABLE Translations;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}