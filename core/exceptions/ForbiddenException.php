<?php

/*******************************
 * Bootstrap ForbiddenException 
 * AUTHOR: RE_WEB
 * @package app\core\ForbiddenException
*/

namespace app\core\exceptions;

class ForbiddenException extends \Exception {

    protected $code = 403;
    protected $message = 'Forbidden page';

}