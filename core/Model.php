<?php

/*******************************
 * Bootstrap Model 
 * AUTHOR: RE_WEB
 * @package app\core\Model
*******************************/

namespace app\core;

abstract class Model {

    public const RULE_REQUIRED = 'required';
    public const RULE_VALID_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public array $errors = [];

    /** 
     * Load properties
     * @return array 
    */

    public function loadData(array $data) {
        foreach ($data as $key => $value)
            if (property_exists($this, $key)) 
                $this->{$key} = $value;
    }

    abstract public function rules(): array;

    public function labels(): array {
        return [];
    }

    /**
     * Getter for label, return attribute if none is present
     * @return label 
    */

    public function getLabel($attribute): string {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /** 
     * Validation method
     * Loops current model and check if certain rules are set
     * If set check if the condition is present
     * Set error rule if true
     * Render
     * @return array 
    */

    public function validate() {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) $ruleName = $rule[0];
                if ($ruleName === self::RULE_REQUIRED && !$value) 
                    $this->setErrorForRule($attribute, self::RULE_REQUIRED);
                if ($ruleName === self::RULE_VALID_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) 
                    $this->setErrorForRule($attribute, self::RULE_VALID_EMAIL);
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min'])
                    $this->setErrorForRule($attribute, self::RULE_MIN, $rule);
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max'])
                    $this->setErrorForRule($attribute, self::RULE_MAX, $rule);
                if ($ruleName === self::RULE_MATCH && !$this->stringCompare($value, $this->{$rule['match']}))
                    $this->setErrorForRule($attribute, self::RULE_MATCH);
                if ($ruleName === SELF::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = $attribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->database->prepare("SELECT * FROM {$tableName} WHERE {$uniqueAttribute} = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) $this->setErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                }
            }
        }

        return empty($this->errors);
    }

    public function stringCompare(string $haystack, string $needle): bool {
        return $haystack === $needle;
    }

    private function setErrorForRule(string $attribute, string $rule, array $params = []): void {
        $message = $this->getErrorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) $message = preg_replace("/\{{$key}\}/", $value, $message);
        $this->errors[$attribute][] = $message;
    }

    public function setError(string $attribute, string $message): void {
       $this->errors[$attribute][] = $message;
    }

    public function getErrorMessages(): array {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_VALID_EMAIL => 'This field must be a valid email',
            self::RULE_MIN => 'This field must contains atleast {min} characters',
            self::RULE_MAX => 'This field must contains a maximum of {max} characters',
            self::RULE_UNIQUE => 'The {field} is already taken'
        ];
    }

    public function getError(string $attribute) {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError(string $attribute): string {
        return $this->errors[$attribute][0] ?? '';
    }

}