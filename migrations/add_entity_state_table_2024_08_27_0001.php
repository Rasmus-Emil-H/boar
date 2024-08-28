<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_entity_state_table_2024_08_27_0001 {

    public function up() {
        (new Schema())->up('state_entity', function(Table $table) {
            $table->increments('StateEntityID');
            $table->varchar('EntityType', 20);
            $table->integer('EntityID', 20);
            $table->integer('StateID', 20);
            $table->timestamp();
            $table->primaryKey('StateEntityID');
            $table->foreignKey('StateID', 'States');
        });
    }

    public function down() {
        (new Schema())->down('state_entity');
    }

}