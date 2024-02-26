<?php

namespace app\core\src\traits;

trait EntityHTTPMethodTrait {

    protected array $allowedHTTPMethods;

    private function processHTTPAction(mixed $requestBody, string $action): void {
		$this->validateHTTPAction(httpBody: $requestBody, httpRequestEntityMethod: $action);

       switch ($action) {
            default:
                break;
        }
	}

    protected function setValidHTTPMethods(array $allowedHTTPMethods): void {
        $this->allowedHTTPMethods = $allowedHTTPMethods;
    }

    protected function validateHTTPAction(mixed $httpBody, string $httpRequestEntityMethod) {
        if (empty($httpBody) || empty($httpRequestEntityMethod)) 
			throw new \app\core\src\exceptions\EmptyException('Arguments can\'t be empty');
        
		if (!isset($this->allowedHTTPMethods) || !in_array($httpRequestEntityMethod, $this->allowedHTTPMethods))
			throw new \app\core\src\exceptions\ForbiddenException('Your action does not exists within the valid scope');
    }

}