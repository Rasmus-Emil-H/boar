<?php

/**
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
*/

namespace app\models;

class QueryModel {

    protected string $query;

    protected array $queryRules = [];

    public function __construct(array $query) {
        $this->query = $query;
    }

    public function validate() {

    }

}