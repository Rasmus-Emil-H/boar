<?php

namespace app\core\src\traits;

trait EntityHTTPMethodTrait {

    protected array $allowedHTTPMethods;

    protected function setValidHTTPMethods(array $allowedHTTPMethods): void {
        $this->allowedHTTPMethods = $allowedHTTPMethods;
    }

    protected function validateHTTPAction(mixed $httpBody, string $httpRequestedEntityMethod) {
        if (empty($httpBody) || empty($httpRequestedEntityMethod)) 
			throw new \app\core\src\exceptions\EmptyException('Arguments can\'t be empty');
        
		if (!isset($this->allowedHTTPMethods) || !in_array($httpRequestedEntityMethod, $this->allowedHTTPMethods))
			throw new \app\core\src\exceptions\ForbiddenException('Your action does not exists within the valid scope');
    }

}