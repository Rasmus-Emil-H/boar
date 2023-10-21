<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_sessions_table_2018_12_15_0001 {

    public function up() {
        (new Schema())->up('Sessions', function(Table $table) {
            $table->increments('SessionID');
            $table->varchar('Value', 50);
            $table->timestamp();
            $table->primaryKey('SessionID');
        });
    }

    public function down() {
        (new Schema())->down('Sessions');
    }

}