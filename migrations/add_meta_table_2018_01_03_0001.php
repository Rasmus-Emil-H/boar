<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_meta_table_2018_01_03_0001 {

    public function up() {
        (new Schema())->up('Meta', function(Table $table) {
            $table->increments('MetaID');
            $table->varchar('EntityType', 20);
            $table->integer('EntityID', 10);
            $table->varchar('IP', 30);
            $table->text('Data');
            $table->primaryKey('MetaID');
        });
    }

    public function down() {
        (new Schema())->down('Meta');
    }

}