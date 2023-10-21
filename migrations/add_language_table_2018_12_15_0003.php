<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_language_table_2018_12_15_0003 {

    public function up() {
        (new Schema())->up('Languages', function(Table $table) {
            $table->increments('LanguageID');
            $table->varchar('TranslationKey', 100);
            $table->varchar('Name', 50);
            $table->timestamp('CreatedAt');
            $table->primaryKey('LanguageID');
        });
    }

    public function down() {
        (new Schema())->drop('Languages');
    }

}