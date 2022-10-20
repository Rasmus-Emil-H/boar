<?php

/*******************************
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
*/

namespace app\models;

use app\core\DbModel;

abstract class UserModel extends DbModel {

    abstract public function getDisplayName(): string;

}