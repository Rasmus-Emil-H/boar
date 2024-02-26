<?php

/**
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
 */

namespace app\core\src;

use \app\core\src\middlewares\Middleware;
use \app\core\src\factories\ControllerFactory;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\controllers\AssetsController;
use \app\core\src\database\Entity;
use \app\core\src\factories\EntityFactory;

class Controller {

    private const DEFAULT_METHOD = 'index';

    protected array $data = [];
    protected array $children = [];

    protected object $requestBody;

    protected string $view = '';
    public string $layout = 'main';
    public string $action = '';
    
    public function __construct(
        protected Request  $request, 
        protected Response $response, 
        protected Session  $session,
        protected AssetsController $clientAssets
    ) {
        $this->requestBody = $this->request->getCompleteRequestBody();
        if ($this->request->isGet()) return;
        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
    }

    public function setData($data): void {
        $merged = array_merge($this->getData(), $data);
        $this->data = $merged;
    }

    public function getData(): array {
        return $this->data;
    }

    protected array $middlewares = [];

    public function setChildren(array $children): void {
        foreach ($children as $child) $this->children[] = $child; 
    }

    public function setChildData(): void {
        foreach ($this->getChildren() as $childController) {
            [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(['handler' => $controller]))->create();
            $cController->{$method}();
            app()->getParentController()->setData($cController->getData());
            $cController->setChildData();
        }
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function registerMiddleware(Middleware $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    protected function returnEntity(): Entity {
        $request = $this->request->getArguments();
        $entityID = CoreFunctions::getIndex($request, 2)->scalar;
        return (new EntityFactory(['handler' => ucfirst(CoreFunctions::first($request)->scalar), 'key' => $entityID]))->create();
    }

    protected function returnValidEntityIfExists(): Entity {
        $entity = $this->returnEntity();
        if (!$entity->exists()) $this->response->redirect('/trip');
        return $entity;
    }

    protected function crudEntity() {
        return $this->returnEntity()->save();
    }

    protected function getClientAssets() {
        return $this->clientAssets;
    }

    public function getView(): string {
        return $this->view ?? View::INVALID_VIEW;
    }

    protected function setView(string $view, string $dir = ''): void {
        $this->view = CoreFunctions::app()->getView()->getTemplatePath($view, $dir);
    }

    public function setLayout(string $layout): void {
        $this->layout = $layout;
    }

    public function setFrontendTemplateAndData(string $templateFile, array $data = []): void {
        $this->setData($data);
        $this->setView($templateFile);
    }

}