<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class edit_meta_table_add_timestamp_2019_12_12_0001 {

    public function up() {
        (new Schema())->table('Meta', function(Table $table) {
            $table->timestamp()->add();
        });
    }

    public function down() {
        (new Schema())->table('Meta', function(Table $table) {
            $table->dropColumns('CreatedAt');
        });
    }

}