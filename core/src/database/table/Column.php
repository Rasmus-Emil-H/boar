<?php

namespace app\core\src\database\table;

class Column {

    protected const PRIMARY_KEY = 'PRIMARY_KEY';
    protected const FOREIGN_KEY = 'FOREIGN_KEY';
    protected const DROP_COLUMN = 'DROP_COLUMN';
    protected const DROP_TABLE  = 'DROP_TABLE';
    protected const ADD_COLUMN  = 'ADD_COLUMN';

    protected string $name;
    protected string $type;
    protected string $previousType; 

    protected array  $options = [];
    protected array  $exclude = ['LENGTH'];

    public function __construct(string $name, string $type, array $options = []) {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    public function get(string $key): string|array {
        return $this->{$key} ?? 'Invalid';
    }

    public function setType(string $type) {
        $this->previousType = $this->type;
        $this->type = $type;
    }

    public function queryString(bool $isAlteringTable = false) {
        try {
            $options = '';
            foreach ( $this->get('options') as $optionKey => $option )  
                $options .= ' ' . (in_array($optionKey, $this->exclude) ? '' : $optionKey) . ' ' . ($option ?? '');
            switch ($this->type) {
                case self::PRIMARY_KEY:
                    $query = " PRIMARY KEY ($this->name) ";
                    break;
                case self::FOREIGN_KEY:
                    $query = ( $isAlteringTable ? 'ADD CONSTRAINT fk_' . $this->foreignColumn : '' ) . " FOREIGN KEY ($this->name) REFERENCES $this->foreignTable($this->foreignColumn)";
                    break;
                case self::DROP_COLUMN:
                    $query = 'DROP COLUMN ' . $this->type . ' ' . $this->name;
                    break;
                case self::ADD_COLUMN:
                    $query = 'ADD COLUMN ' . $this->name . ' ' . $this->previousType . $options;
                    break;
                default:
                    $query = $this->name . ' ' .  $this->type . $options;
                    break;
            }
            return $query;
        } catch (\Exception $e) {
            throw new \app\core\exceptions\NotFoundException("Column generation failed: " . $e->getMessage());
        }
    }

}