<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_meta_table_2019_01_03_0001 {

    public function up() {
        (new Schema())->up('Meta', function(Table $table) {
            $table->increments('MetaID');
            $table->varchar('EntityType', 20);
            $table->integer('EntityID', 10);
            $table->text('Data');
            $table->varchar('IP', 40);
            $table->primaryKey('MetaID');
        });
    }

    public function down() {
        (new Schema())->down('Meta');
    }

}