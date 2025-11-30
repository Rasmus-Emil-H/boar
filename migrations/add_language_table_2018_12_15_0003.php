<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;
use \app\models\LanguageModel;

class add_language_table_2018_12_15_0003 {

    public function up() {
        (new Schema())->up('Languages', function(Table $table) {
            $table->increments('LanguageID');
            $table->varchar('Name', 20);
            $table->varchar('Code', 3);
            $table->timestamp('CreatedAt');
            $table->primaryKey('LanguageID');
        });

        (new LanguageModel([
            'Name' => 'English',
            'Code' => 'EN'    
        ]))->save();
    }

    public function down() {
        (new Schema())->down('Languages');
    }

}