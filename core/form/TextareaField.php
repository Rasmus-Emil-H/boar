<?php

/*******************************
 * Bootstrap TextareaField 
 * AUTHOR: RE_WEB
 * @package app\core\TextareaField
*/

namespace app\core\form;

use \app\core\Model;

class TextareaField extends BaseField {

    public function renderInput(): string {
        return sprintf('<textarea name="%s" class="form-control %s">%s</textarea>',
            $this->attribute,
            $this->model->getError($this->attribute) ? 'is-invalid' : '',
            $this->model->{$this->attribute}
        );
    }

}