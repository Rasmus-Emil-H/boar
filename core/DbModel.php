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
    abstract public function getForeignKeys(): array;
    abstract public function getPrimaryKey(): string;
        
    public function setAttributes(array $attributes) {
        foreach ( $attributes as $key => $value ) $this->{$key} = $value;
    }

    public function getCurrentProperties(): array {
        $props = [];
        foreach ( $this->getAttributes() as $key => $value ) $props[$value] = $this->{$value};
        return $props;
    }

    public function init(string $addition = '') {
		$fields = $this->getAttributes();
        foreach ( $fields as $key => $value ) $initValues[] = $value;
		return Application::$app->database->init($this->tableName(), $fields, $initValues);
	}

    public function prepareCreate() {
        $attributes = $this->getAttributes();
        $params = array_map(fn($attr) => ":{$attr}", $attributes);
        return $this->prepare("INSERT INTO {$this->tableName()} (". implode(',', $attributes) .") VALUES (". implode(',', $params) .")");
    }

    public function prepareUpdate() {
        $primaryKey = $this->getPrimaryKey();
        $updateValues = '';
        $placeholders = $this->getCurrentProperties();
        foreach ( $placeholders as $key => $value ) $updateValues .= "{$key} = :{$key}" . (array_key_last($this->getCurrentProperties()) === $key ? '' : ', ');
        return $this->prepare("UPDATE {$this->tableName()} SET ". $updateValues ." WHERE {$primaryKey} = {$this->{$this->getPrimaryKey()}}");
    }

    public function save() {
        $exists = $this->findOne([$this->getPrimaryKey() => $this->{$this->getPrimaryKey()}], $this->tableName());
        $statement = !$exists ? $this->prepareCreate() : $this->prepareUpdate();
        foreach ($this->getAttributes() as $attribute) {
            if ( $attribute === 'placeholder' ) continue;
            $statement->bindValue(":{$attribute}", $this->{$attribute});
        }
        $statement->execute();
    }

    public function debug($statement): void {
        var_dump($statement->debugDumpParams());
    } 

    public function prepare(string $sql) {
        return Application::$app->database->pdo->prepare($sql);
    }

    public function findOne(array $where, string $tableName) {
        $sql = implode(" AND ", array_map(fn($attr) => "{$attr} = :{$attr}", array_keys($where)));
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