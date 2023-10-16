<?php

/**
 * Table builder
 */

namespace app\core\database\table;

class Table {

    protected $name;
    protected $columns = [];

    private const INT_COLUMN_TYPE = 'int';
    private const VARCHAR_COLUMN_TYPE = 'varchar';
    private const TEXT_COLUMN_TYPE = 'text';

    public function __construct($name) {
        $this->name = $name;
    }

    public function increments(string $columnName) {
        $this->columns[] = new Column($columnName, self::INT_COLUMN_TYPE, ['auto_increment' => true]);
    }

    public function string(string $columnName, int $length = 75) {
        $this->columns[] = new Column($columnName, self::VARCHAR_COLUMN_TYPE, ['length' => $length]);
    }

    public function text(string $columnName) {
        $this->columns[] = new Column($columnName, self:: TEXT_COLUMN_TYPE);
    }

    public function integer(string $columnName) {
        $this->columns[] = new Column($columnName, self:: INT_COLUMN_TYPE);
    }

    public function primary() {
        $this->columns[count($this->columns) - 1]->setPrimary();
    }

    public function foreign(string $columnName, string $foreignTable, string $foreignColumn) {
        $this->columns[] = new ForeignKey($columnName, $foreignTable, $foreignColumn);
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function getName(): string {
        return $this->name;
    }

}