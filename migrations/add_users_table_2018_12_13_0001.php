<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_users_table_2018_12_13_0001 {

    public function up() {
        (new Schema())->up('Users', function(Table $table) {
            $table->increments('UserID');
            $table->varchar('Email', 100);
            $table->varchar('Name', 50);
            $table->integer('Status', 1);
            $table->timestamp();
            $table->primaryKey('UserID');
        });
    }

    public function down() {
        (new Schema())->down('Users');
    }

}