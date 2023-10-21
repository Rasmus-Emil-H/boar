<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_meta_table_2019_01_03_0001 {

    public function up() {
        (new Schema())->up('Meta', function(Table $table) {
            $table->increments('MetaID');
            $table->varchar('EntityType', 20);
            $table->integer('EntityID', 10);
            $table->timestamp();
            $table->primaryKey('MetaID');
        });
    }

    public function down() {
        (new Schema())->drop('Meta');
    }

}