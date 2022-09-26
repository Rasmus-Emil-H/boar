<?php

/*******************************
 * Bootstrap RegisterModel 
 * AUTHOR: RE_WEB
 * @package app\models\RegisterModel
*/

namespace app\models;

use app\core\Model;

class RegisterModel extends Model {

    public string $email;
    public string $password;

    public function register() {
        echo 'creating new user';
    }

}