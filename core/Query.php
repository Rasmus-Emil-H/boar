<?php

/**
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\core\Query
 * Minor thoughts for some query validation at some point
 */

namespace app\core;

use app\models\QueryModel;

class Query extends QueryModel {

    protected QueryModel $queryModel;

    public function __construct() {
        $this->queryModel = new QueryModel($_SERVER['REQUEST_URI']);
        $this->validate();
    }

}