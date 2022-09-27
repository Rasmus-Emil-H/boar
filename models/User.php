<?php

/*******************************
 * Bootstrap RegisterModel 
 * AUTHOR: RE_WEB
 * @package app\models\RegisterModel
*/

namespace app\models;

use app\core\DbModel;

class User extends DbModel {

    public string $email = '';
    public string $password = '';

    public function tableName(): string {
        return 'Users';
    }

    /*
     * Register method 
     * @return boolean
    */

    public function register() {
        $this->save();
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

    public function getAttributes(): array {
        return ['firstname', 'lastname', 'email', 'password'];
    }

}