<?php

/**
|----------------------------------------------------------------------------
| Default controller
|----------------------------------------------------------------------------
| 
| @author RE_WEB
| @package \app\core\src
|
*/

namespace app\core\src;

use \app\core\src\middlewares\Middleware;
use \app\core\src\factories\ControllerFactory;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\controllers\AssetsController;
use \app\core\src\database\Entity;
use \app\core\src\factories\EntityFactory;
use \app\core\src\traits\ControllerMethodTrait;
use \app\core\src\traits\ControllerAssetTrait;

class Controller {

    use ControllerMethodTrait;
    use ControllerAssetTrait;

    private const DEFAULT_METHOD = 'index';
    private const EXPECTED_ENTITY_ID_POSITION = 2;

    protected array $data = [];
    protected array $children = [];
    protected array $middlewares = [];

    protected object $requestBody;

    protected string $view = '';
    public string $layout = 'main';
    public string $action = '';
    
    public function __construct(
        protected Request  $request, 
        protected Response $response, 
        protected AssetsController $clientAssets
    ) {
        $this->requestBody = $this->request->getCompleteRequestBody();
        $this->validateCSRFToken();
    }

    private function validateCSRFToken() {
        if ($this->request->isGet()) return;
        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
    }

    public function setData($data): void {
        $merged = array_merge($this->getData(), $data);
        $this->data = $merged;
    }

    public function upsertData(string $key, mixed $data): void {
        $this->data[$key][] = $data;
    }

    public function getData(): array {
        return $this->data;
    }

    public function getDataKey(string $key): mixed {
        return $this->data[$key] ?? null;
    }

    public function setChildren(array $children): void {
        foreach ($children as $child) $this->children[] = $child; 
    }

    public function setChild(string $key, mixed $child): void {
        $this->children[$key] = $child; 
    }

    public function setChildData(): void {
        $parentController = app()->getParentController();

        array_map(function($childController) use ($parentController) {
            [$handler, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(compact('handler')))->create();
            $cController->{$method}();

            $parentController->setData($cController->getData());
            $cController->setChildData();
        }, $this->getChildren());
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function getChild(string $child): ?object {
        return $this->children[$child] ?? null;
    }

    public function upsertChildData(array $data) {
        $parentController = app()->getParentController();

        foreach ($data as $key => $childController) {
            [$handler, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(compact('handler')))->create();
            $cController->{$method}();

            $parentController->data[$key] = $cController->getData();
        }
    }

    public function registerMiddleware(Middleware $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    protected function returnEntity(): Entity {
        $request = $this->request->getArguments();

        $key = CoreFunctions::getIndex($request, self::EXPECTED_ENTITY_ID_POSITION)->scalar;
        $handler = ucfirst(CoreFunctions::first($request)->scalar);

        return (new EntityFactory(compact('handler', 'key')))->create();
    }

    protected function returnValidEntityIfExists(): Entity {
        $entity = $this->returnEntity();
        return $entity;
    }

}