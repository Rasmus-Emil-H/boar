<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_entity_language_table_2024_10_29_0001 {

    public function up() {
        (new Schema())->up('entity_language', function(Table $table) {
            $table->increments('EntityLanguageID');
            $table->varchar('EntityType', 50);
            $table->integer('EntityID', 15);
            $table->integer('LanguageID', 15);
            $table->timestamp();
            $table->primaryKey('EntityLanguageID');
            $table->foreignKey('LanguageID', 'Languages');
        });
    }

    public function down() {
        (new Schema())->down('entity_language');
    }

}