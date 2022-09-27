<?php

/*******************************
 * Bootstrap RegisterModel 
 * AUTHOR: RE_WEB
 * @package app\models\RegisterModel
*/

namespace app\models;

use app\core\DbModel;

class User extends DbModel {

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_DELETED  = 2;

    public string $email = '';
    public string $password = '';
    public int $status = self::STATUS_INACTIVE;
    public string $firstname = '';
    public string $lastname = '';

    public function tableName(): string {
        return 'Users';
    }

    /*
     * Register method 
     * @return boolean
    */

    public function save() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return parent::save();
    }

    /** 
     * Validation rules for user registration
     * @return array 
    */

    public function rules(): array {
        return [
            'email' => [
                self::RULE_REQUIRED, self::RULE_VALID_EMAIL, 
                [self::RULE_UNIQUE, 'class' => self::class]
            ],
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'password' => [
                self::RULE_REQUIRED, 
                [self::RULE_MIN, 'min' => 8], 
                [self::RULE_MAX, 'max' => 255]
            ],
        ];
    }

    public function getAttributes(): array {
        return ['firstname', 'lastname', 'email', 'status', 'password'];
    }

}