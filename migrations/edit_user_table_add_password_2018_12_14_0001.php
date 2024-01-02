<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class edit_user_table_add_password_2018_12_14_0001 {

    public function up() {
        (new Schema())->table('Users', function(Table $table) {
            $table->varchar('Password', 255)->add();
        });
    }

    public function down() {
        (new Schema())->table('Users', function(Table $table) {
            $table->dropColumns(['Password']);
        });
    }

}