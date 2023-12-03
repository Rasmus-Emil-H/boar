<?php

use \app\core\database\table\Table;
use \app\core\database\Schema;

class add_translations_table_2018_12_16_0001 {

    public function up() {
        (new Schema())->up('Translations', function(Table $table) {
            $table->increments('TranslationID');
            $table->varchar('Translation', 100);
            $table->integer('LanguageID', 2);
            $table->varchar('TranslationHash', 50);
            $table->timestamp();
            $table->primaryKey('TranslationID');
            $table->foreignKey('LanguageID', 'Languages', 'LanguageID');
        });
    }

    public function down() {
        (new Schema())->down('Translations'); 
    }

}