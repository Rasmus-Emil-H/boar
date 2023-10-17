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
        (new \app\core\database\Schema())->create('test', function($table) {
            $table->string('qwd');
            $table->text('d');
            $table->increments('id');
            $table->primary('id');
        });
        exit;
    }

    public function down() {
        $SQL = "DROP TABLE Translations;";
        app()->connection->rawSQL($SQL);
        app()->connection->execute();
    }

}