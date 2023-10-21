<?php

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