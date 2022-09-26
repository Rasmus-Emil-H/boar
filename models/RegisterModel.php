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

    /*
     * Register method 
     * @return boolean
    */

    public function register() {
        echo 'creating new user';
    }

    /** 
     * Validation rules for user registration
     * @return array 
    */

    public function rules(): array {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_VALID_EMAIL],
            'password' => [
                self::RULE_REQUIRED, 
                [self::RULE_MIN, 'min' => 8], 
                [self::RULE_MAX, 'max' => 255]
            ],
        ];
    }

}