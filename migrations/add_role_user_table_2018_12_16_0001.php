<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_role_user_table_2018_12_16_0001 {

    public function up() {
        (new Schema())->up('role_user', function(Table $table) {
            $table->increments('PivotID');
            $table->integer('UserID', 10);
            $table->integer('RoleID', 2);
            $table->timestamp();
            $table->primaryKey('PivotID');
        });
    }

    public function down() {
        (new Schema())->down('role_user');
    }

}