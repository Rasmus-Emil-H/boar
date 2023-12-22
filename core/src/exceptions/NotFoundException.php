<?php

/**
 * Bootstrap NotFoundException 
 * AUTHOR: RE_WEB
 * @package app\core\NotFoundException
 */

namespace app\core\src\exceptions;

class NotFoundException extends \Exception {

    protected $code = 404;
    protected $message = 'Not found';

}