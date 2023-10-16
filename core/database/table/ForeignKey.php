<?php

class ForeignKey extends Column {

    protected $foreignTable;
    protected $foreignColumn;
    
    private const DEFAULT_COLUMN_TYPE = 'int';
    
    public function __construct(string $name, string $foreignTable, string $foreignColumn) {
        parent::__construct($name, self::DEFAULT_COLUMN_TYPE);
        $this->foreignTable = $foreignTable;
        $this->foreignColumn = $foreignColumn;
    }
    
}