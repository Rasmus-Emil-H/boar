<?php

/**
|----------------------------------------------------------------------------
| Automatically created migration
|----------------------------------------------------------------------------
|
| Adjust table specifications to your needs
|
*/

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_auth_table_2025_01_29_0001 {
    public function up() {
        (new Schema())->up('Auth', function(Table $table) {
            $table->increments('AuthID');
            $table->timestamp();
            $table->primaryKey('AuthID');
        });
    }

    public function down() {
        (new Schema())->down('Auth');
    }
}