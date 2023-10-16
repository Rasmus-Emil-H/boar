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

}