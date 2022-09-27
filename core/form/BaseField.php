<?php

/*******************************
 * Bootstrap BaseField 
 * AUTHOR: RE_WEB
 * @package app\core\BaseField
*/

namespace app\core\form;

use \app\core\Model;

abstract class BaseField {

    abstract public function renderInput(): string;

    public Model $model;
    public string $attribute;

    public function __construct(Model $model, string $attribute) {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString(): string {
        return sprintf('
            <div class="form-group">
                <label for="exampleInputEmail1">%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ', 
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }

}