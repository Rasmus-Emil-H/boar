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
    abstract public function getPrimaryKey(): string;
        
    public function setAttributes(array $attributes) {
        foreach ( $this->getAttributes() as $key => $value )
            $this->{$value} = $attributes[$key];
    }

    public function getCurrentProperties(): array {
        $props = [];
        foreach ( $this->getAttributes() as $key => $value ) $props[$value] = $this->{$value};
        return $props;
    }

    public function prepareCreate(string $table, array $attributes, array $params) {
        return $this->prepare("INSERT INTO {$table} (". implode(',', $attributes) .") VALUES (". implode(',', $params) .")");
    }

    public function prepareUpdate() {
        $primaryKey = $this->getPrimaryKey();
        $updateValues = '';
        foreach ( $this->getCurrentProperties() as $key => $value ) $updateValues .= "{$key} = :{$key}" . (array_key_last($this->getCurrentProperties()) === $key ? '' : ', ');
        return $this->prepare("UPDATE {$table} SET ". $updateValues ." WHERE {$primaryKey} = {$exists->{$primaryKey}}");
    }

    public function save() {
        $table = $this->tableName();
        $attributes = $this->getAttributes();
        $params = array_map(fn($attr) => ":{$attr}", $attributes);
        $exists = $this->findOne([$this->getPrimaryKey() => $this->{$this->getPrimaryKey()}], $table);
        $statement = !$exists ? $this->prepareCreate() : $this->prepareUpdate();
        foreach ($attributes as $attribute) $statement->bindValue(":{$attribute}", $this->{$attribute});
        return $statement->execute();
    }

    public function prepare(string $sql) {
        return Application::$app->database->pdo->prepare($sql);
    }

    public function findOne(array $where, string $tableName) {
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "{$attr} = :{$attr}", $attributes));
        $statement = $this->prepare("SELECT * FROM {$tableName} WHERE {$sql}");
        foreach ($where as $key => $value) $statement->bindValue(":{$key}", $value);
        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    public function remove() {
        Application::$app->database
            ->delete($this->tableName())
            ->where([$this->getPrimaryKey() => $this->{$this->getPrimaryKey()}])
            ->execute();
    }

}