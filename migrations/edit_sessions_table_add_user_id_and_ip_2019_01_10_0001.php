<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class edit_sessions_table_add_user_id_and_ip_2019_01_10_0001 {

    public function up() {
        (new Schema())->table('Sessions', function(Table $table) {
            $table->integer('UserID')->add();
        });
    }

    public function down() {
        (new Schema())->table('Meta', function(Table $table) {
            $table->dropColumns(['UserID', 'IP']);
        });
    }

}