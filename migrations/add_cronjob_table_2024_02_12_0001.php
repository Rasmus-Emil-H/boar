<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_cronjob_table_2024_02_12_0001 {

    public function up() {
        (new Schema())->up('Cronjob', function(Table $table) {
            $table->increments('CronjobID');
            $table->varchar('CronjobEntity');
            $table->timestamp();
            $table->primaryKey('CronjobID');
        });
    }

    public function down() {
        (new Schema())->down('Cronjob');
    }

}