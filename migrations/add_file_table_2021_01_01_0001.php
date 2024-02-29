<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_file_table_2021_01_01_0001 {

    public function up() {
        (new Schema())->up('Files', function(Table $table) {
            $table->increments('FileID');
            $table->varchar('Name', 255);
            $table->varchar('Path', 255);
            $table->varchar('Type', 50);
            $table->varchar('Hash', 100);
            $table->timestamp();
            $table->primaryKey('FileID');
        });
    }

    public function down() {
        (new Schema())->down('Files');
    }

}