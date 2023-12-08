<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class edit_sessions_table_add_user_id_2019_12_23_0001 {

    public function up() {
        (new Schema())->table('Sessions', function(Table $table) {
            $table->integer('UserID', 10)->add();
            $table->foreignKey('UserID', 'Users', 'UserID');
        });
    }

    public function down() {
        (new Schema())->table('Sessions', function(Table $table) {
            $table->dropColumns(['UserID']);
        });
    }

}