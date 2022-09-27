<?php

/*******************************
 * Bootstrap DbModel 
 * AUTHOR: RE_WEB
 * @package app\core\DbModel
*/

namespace app\core;

abstract class DbModel extends Model {

    abstract public function tableName(): string;

    abstract public function getAttributes(): array;

    public function save() {
        $table = $this->tableName();
        $attributes = $this->getAttributes();
        $params = array_map(fn($attr) => ":{$attr}", $attributes);
        $statement = self::prepare("INSERT INTO {$table} (". implode(',', $attributes) .") VALUES (". implode(',', $params) .")");
        foreach ($attributes as $attribute) $statement->bindValue(":{$attribute}", $this->{$attribute});
        return $statement->execute();
    }

    public static function prepare(string $sql) {
        return Application::$app->database->pdo->prepare($sql);
    }

}