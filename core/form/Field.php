<?php

/*******************************
 * Bootstrap Field 
 * AUTHOR: RE_WEB
 * @package app\core\Field
*/

namespace app\core\form;

use app\core\Model;

class Field {

    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    

    public Model $model;
    public string $attribute;
    public string $type;

    public function __construct(Model $model, string $attribute) {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->type = self::TYPE_TEXT;
    }

    public function __toString(): string {
        return sprintf('
            <div class="form-group">
                <label for="exampleInputEmail1">%s</label>
                <input name="%s" type="%s" value="%s" class="form-control %s">
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ', 
            $this->attribute, 
            $this->attribute,
            $this->type,
            $this->model->{$this->attribute},
            $this->model->getError($this->attribute) ? 'is-invalid' : '',
            $this->model->getFirstError($this->attribute)
        );
    }

    public function passwordField(): Field {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function emailField(): Field {
        $this->type = self::TYPE_EMAIL;
        return $this;
    }

}