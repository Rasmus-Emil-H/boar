<?php

/*******************************
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\core\Query
 * Minor thoughts for some query validation at some point
 * 
*/

namespace app\core;

use app\models\QueryModel;

class Query extends QueryModel {

    protected QueryModel $queryModel;

    public function __construct() {
        parent::__construct($_GET);
    }

}