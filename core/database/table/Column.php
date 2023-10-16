<?php

namespace app\core\database\table;

class Column {

    protected $name;
    protected $type;
    protected $options = [];

    public function __construct(string $name, string $type, array $options = []) {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    public function setPrimary() {
        $this->options['primary'] = true;
    }

    public function get(string $key) {
        return $this->{$key} ?? 'Invalid';
    }

    public function queryString(): string {
        return 
            $this->name . ' ' . 
            strtoupper($this->type) .
            (isset($this->get('options')['length']) ? '('.$this->get('options')['length'].')' : null);
    }

}