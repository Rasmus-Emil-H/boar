<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_file_entity_table_28_02_2024_0001 {

    public function up() {
        (new Schema())->up('file_entity', function(Table $table) {
            $table->increments('FileEntityID');
            $table->varchar('EntityType', 50);
            $table->integer('EntityID', 15);
            $table->integer('FileID', 15);
            $table->timestamp();
            $table->primaryKey('FileEntityID');
            $table->foreignKey('FileID', 'Files');
        });
    }

    public function down() {
        (new Schema())->down('Files');
    }

}