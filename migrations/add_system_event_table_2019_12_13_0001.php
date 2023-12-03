<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_system_event_table_2019_12_13_0001 {

    public function up() {
        (new Schema())->up('SystemEvent', function(Table $table) {
            $table->increments('SystemEventID');
            $table->text('Data');
            $table->timestamp('CreatedAt');
            $table->primaryKey('SystemEventID');
        });
    }

    public function down() {
        (new Schema())->down('SystemEvent');
    }

}