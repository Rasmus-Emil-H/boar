<?php

/**
 * Table builder
 */

namespace app\core\database\table;

class Table {

    protected $name;
    protected $columns = [];

    private const INT_COLUMN_TYPE = 'INT';
    private const VARCHAR_COLUMN_TYPE = 'VARCHAR';
    private const TEXT_COLUMN_TYPE = 'TEXT';
    private const PRIMARY_KEY = 'PRIMARY_KEY';
    private const TIMESTAMP = 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';

    public function __construct($name) {
        $this->name = $name;
    }

    public function createColumn(string $columnName, string $type, array $options = []) {
        $this->columns[] = new Column($columnName, $type, $options);
    }

    public function increments(string $columnName) {
        $this->createColumn($columnName, self::INT_COLUMN_TYPE, ['AUTO_INCREMENT' => null]);
    }

    public function varchar(string $columnName, int $length = 75) {
        $this->createColumn($columnName, self::VARCHAR_COLUMN_TYPE, ['LENGTH' => '('.$length.')']);
    }

    public function text(string $columnName) {
        $this->createColumn($columnName, self::TEXT_COLUMN_TYPE);
    }

    public function integer(string $columnName) {
        $this->createColumn($columnName, self::INT_COLUMN_TYPE);
    }

    public function primaryKey(string $columnName) {
        $this->createColumn($columnName, self::PRIMARY_KEY);
    }

    public function timestamp(string $columnName = 'CreatedAt') {
        $this->createColumn($columnName, self::TIMESTAMP);
    }

    public function foreignKey(string $columnName, string $foreignTable, string $foreignColumn) {
        $this->columns[] = new ForeignKey($columnName, $foreignTable, $foreignColumn);
    }

    public function dropColumns(array|string $columns) {
        if (is_string($columns)) (array)$columns;
        foreach ( $columns as $column ) {
            $static = new Column($columnName);
        }
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function getName(): string {
        return $this->name;
    }

}