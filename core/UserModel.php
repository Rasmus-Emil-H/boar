<?php

/*******************************
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
*/

namespace app\core;

abstract class UserModel extends DbModel {

    abstract public function getDisplayName(): string;

}