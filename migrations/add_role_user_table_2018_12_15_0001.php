<?php

/**
 * Initial migration
 * @someday i'll refactor this so that
 * it would go something like
 * $this->db->table->field('name')->type('varchar')->length(255)
 * @return migration
*/

use \app\core\Application;

class add_role_user_table_2018_12_15_0001 {

    public function up() {
        $SQL = "CREATE TABLE IF NOT EXISTS role_user (
            PivotID INT AUTO_INCREMENT PRIMARY KEY,
            UserID INT(10),
            RoleID INT(2),
            FOREIGN KEY (UserID) REFERENCES Users(UserID),
            FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
        )";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

    public function down() {
        $SQL = "DROP TABLE Roles;";
        Application::$app->connection->rawSQL($SQL);
        Application::$app->connection->execute();
    }

}