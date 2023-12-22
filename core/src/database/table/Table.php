<?php

/**
 * Table builder
 */

namespace app\core\src\database\table;

class Table {

    protected $name;
    protected $columns = [];

    public const DELETED_AT_COLUMN = 'DeletedAt';
    public const STATUS_COLUMN = 'Status';

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
        return $this;
    }

    public function varchar(string $columnName, int $length = 75) {
        $this->createColumn($columnName, self::VARCHAR_COLUMN_TYPE, ['LENGTH' => '('.$length.')']);
        return $this;
    }

    public function text(string $columnName) {
        $this->createColumn($columnName, self::TEXT_COLUMN_TYPE);
        return $this;
    }

    public function integer(string $columnName) {
        $this->createColumn($columnName, self::INT_COLUMN_TYPE);
        return $this;
    }

    public function primaryKey(string $columnName) {
        $this->createColumn($columnName, self::PRIMARY_KEY);
        return $this;
    }

    public function timestamp(string $columnName = 'CreatedAt') {
        $this->createColumn($columnName, self::TIMESTAMP);
        return $this;
    }

    public function foreignKey(string $columnName, string $foreignTable, string $foreignColumn) {
        $this->columns[] = new ForeignKey($columnName, $foreignTable, $foreignColumn);
        return $this;
    }

    public function add() {
        last($this->getColumns())->setType('ADD_COLUMN');
    }

    public function drop() {
        last($this->getColumns())->setType('DROP_COLUMN');
    }

    public function dropColumns(array|string $columns) {
        if (is_string($columns)) (array)$columns;
        foreach ( $columns as $column ) {
            $static = new Column($column, 'drop');
        }
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function getName(): string {
        return $this->name;
    }

}