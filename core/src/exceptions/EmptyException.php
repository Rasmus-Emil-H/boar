<?php

/**
 * Bootstrap ForbiddenException 
 * AUTHOR: RE_WEB
 * @package app\core\ForbiddenException
 */

namespace app\core\src\exceptions;

class EmptyException extends \Exception {

    protected $code = 400;
    protected $message = 'Data is empty where it should\'nt be';

}