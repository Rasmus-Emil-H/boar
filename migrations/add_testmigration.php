<?php

/**
 * @return migration
*/

use \app\core\database\Schema;

class add_testmigration {

    public function up() {
        (new Schema())->create('test', function($table) {
            $table->string('qwd');
            $table->text('d');
            $table->increments('id');
            $table->primary('id');
        });
    }

    public function down() {
        (new Schema())->drop('test');
    }

}