<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_system_event_table_2019_12_13_0001 {

    public function up() {
        (new Schema())->up('SystemEvents', function(Table $table) {
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