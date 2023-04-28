<?php

/*******************************
 * Bootstrap AuthMiddleware 
 * AUTHOR: RE_WEB
 * @package app\core\AuthMiddleware
*/

namespace app\core\middlewares;

use app\core\exceptions\ForbiddenException;
use app\core\Application;

class InvalidArgument extends Middleware {

	public array $validArguments = [];
	public string $currentArgument;

	public function __construct(array $validArguments = [], string $currentArgument) {
		$this->validArguments = $validArguments;
		$this->currentArgument = $currentArgument;
	}

	public function execute() {
		if ( !in_array($this->currentArgument, $this->validArguments) && $this->currentArgument !== '' ) 
			throw new ForbiddenException();
	}

}