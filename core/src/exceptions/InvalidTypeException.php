<?php

/**
 * Bootstrap NotFoundException 
 * AUTHOR: RE_WEB
 * @package app\core\NotFoundException
 */

namespace app\core\src\exceptions;

class InvalidTypeException extends \Exception {

    protected $code = 409;
    protected $message = 'Invalid type was provided';

}