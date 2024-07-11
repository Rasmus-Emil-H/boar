<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_polymorphich_comment_table_2022_02_02_0001 {

    public function up() {
        (new Schema())->up('Comments', function(Table $table) {
            $table->increments('CommentID');
            $table->varchar('EntityType', 20);
            $table->integer('EntityID');
            $table->text('Comment');
            $table->varchar('Type', 50);
            $table->timestamp();
            $table->primaryKey('CommentID');
        });
    }

    public function down() {
        (new Schema())->down('Comments');
    }

}