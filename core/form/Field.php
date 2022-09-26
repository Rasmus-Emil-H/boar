<?php

/*******************************
 * Bootstrap Field 
 * AUTHOR: RE_WEB
 * @package app\core\Field
*/

namespace app\core\form;

use app\core\Model;

class Field {

    public Model $model;
    public string $attribute;

    public function __construct(Model $model, string $attribute) {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString() {
        return sprintf('
            <div class="form-group">
                <label for="exampleInputEmail1">%s</label>
                <input name="%s" type="text" value="%s" class="form-control %s">
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ', 
            $this->attribute, 
            $this->attribute, 
            $this->model->{$this->attribute},
            $this->model->getError($this->attribute) ? 'is-invalid' : '',
            $this->model->getFirstError($this->attribute)
        );
    }

}