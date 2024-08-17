<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_push_table_2024_08_14_0001 {

    public function up() {
        (new Schema())->up('Push', function(Table $table) {
            $table->increments('PushID');
            $table->varchar('Endpoint', 200);
            $table->varchar('ExpirationTime', 50);
            $table->integer('UserID', 10);
            $table->text('PubSubKeys');
            $table->timestamp();
            $table->primaryKey('PushID');
        });
    }

    public function down() {
        (new Schema())->down('Push');
    }

}