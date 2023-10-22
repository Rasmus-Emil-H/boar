<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class edit_sessions_table_add_user_id_and_ip_2019_01_10_0001 {

    public function up() {
        (new Schema())->table('Meta', function(Table $table) {
            $table->integer('UserID');
            $table->varchar('IP', 20);
        });
    }

    public function down() {
        (new Schema())->table('Meta', function(Table $table) {
            $table->dropColumns(['UserID', 'IP']);
        });
    }

}