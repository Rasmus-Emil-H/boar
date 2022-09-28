<?php

/*******************************
 * Bootstrap PostForm 
 * AUTHOR: RE_WEB
 * @package app\models\PostForm
*******************************/

namespace app\models;

use app\core\Model;

class PostForm extends Model {

    public string $email = '';
    public string $message = '';

    public function send() {  
        return true;
    }

    public function labels(): array {
        return [
            'email' => 'Email',
            'message' => 'Enter your message'
        ];
    }

    public function rules(): array {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_VALID_EMAIL],
            'message' => [self::RULE_REQUIRED]
        ];
    }

}