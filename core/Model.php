<?php

/*******************************
 * Bootstrap Model 
 * AUTHOR: RE_WEB
 * @package app\core\Model
*/

namespace app\core;

abstract class Model {

    public const RULE_REQUIRED = 'required';
    public const RULE_VALID_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';

    public array $errors = [];

    public function loadData(array $data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;

    public function validate() {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            var_dump($value);
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) $ruleName = $rule[0];
                if ($ruleName === self::RULE_REQUIRED && !$value) $this->setError($attribute, self::RULE_REQUIRED);
                if ($ruleName === self::RULE_VALID_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) $this->setError($attribute, self::RULE_VALID_EMAIL);
            }
        }

        return empty($this->errors);
    }

    public function setError(string $attribute, string $rule): void {
        $message = $this->getErrorMessages()[$rule] ?? '';
        $this->errors[$attribute][] = $message;
    }

    public function getErrorMessages(): array {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_VALID_EMAIL => 'This field must be a valid email',
            self::RULE_MIN => 'This field must contains atleast {min}',
            self::RULE_MAX => 'This field must contains a maximum of {max} characters'
        ];
    }

}