<?php

/*******************************
 * Bootstrap Field 
 * AUTHOR: RE_WEB
 * @package app\core\Field
*/

namespace app\core\form;

use \app\core\Model;

class InputField extends BaseField {

    public const TYPE_TEXT     = 'text';
    public const TYPE_EMAIL    = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_DATE     = 'date';
    public const TYPE_HIDDEN   = 'hidden';

    public Model $model;
    public string $attribute;
    public string $type;

    public function __construct(Model $model, string $attribute) {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function renderInput(): string {
        return sprintf('<input name="%s" type="%s" value="%s" class="form-control %s">',
            $this->attribute,
            $this->type,
            $this->model->{$this->attribute},
            $this->model->getError($this->attribute) ? 'is-invalid' : '',
        );
    }

    public function hiddenField(): InputField {
        $this->type = self::TYPE_HIDDEN;
        return $this;
    }

    public function passwordField(): InputField {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function emailField(): InputField {
        $this->type = self::TYPE_EMAIL;
        return $this;
    }

    public function dateField(): InputField {
        $this->type = self::TYPE_DATE;
        return $this;
    }

}