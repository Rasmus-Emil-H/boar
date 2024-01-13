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

class Controller {

    private const DEFAULT_METHOD = 'index';

    protected array $data = [];
    protected array $children = [];

    protected string $view = '';
    public string $layout = 'main';
    public string $action = '';
    
    public function __construct(
        protected Request  $request, 
        protected Response $response, 
        protected Session  $session,
        protected AssetsController $clientAssets
    ) {

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

    /**
     * Get data from child
     * Then set data on instantiated controller
     * @param array [strings of to be \app\core\Controller]
     * @param \app\core\src\controller Parent controller
     * @return void
     */

    public function setChildData(): void {
        foreach ($this->getChildren() as $childController) {
            [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(['handler' => $controller]))->create();
            $cController->{$method}();
            CoreFunctions::app()->getParentController()->setData($cController->getData());
            $cController->setChildData();
        }
    }

    public function getView(): string {
        return $this->view ?? View::INVALID_VIEW;
    }

    protected function setView(string $view, string $dir = '') {
        $this->view = $this->getTemplatePath($view, $dir);
    }

    public function getPartialTemplate(string $partial): string {
        return $this->getTemplatePath($partial, 'partials/');
    }

    public function getTemplate(string $partial): string {
        return $this->getTemplatePath($partial, '');
    }

    public function getTemplatePath(string $template, string $dir): string {
        return CoreFunctions::app()::$ROOT_DIR .  File::VIEWS_FOLDER . $dir . $template . File::TPL_FILE_EXTENSION;
    }

    public function setLayout(string $layout): void {
        $this->layout = $layout;
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

    private function returnEntity(): \app\core\src\database\Entity {
        $request = $this->request->getArguments();
        $entityID = CoreFunctions::getIndex($request, 2)->scalar;
        $entity = new $this->entity($entityID);
        return $entity;
    }

    protected function isViewingValidEntity(): void {
        $entity = $this->returnEntity();
        if (!$entity->exists()) $this->response->redirect('/home');
    }

    protected function crudEntity() {
        return $this->returnEntity()->save();
    }

    protected function getClientAssets() {
        return $this->clientAssets;
    }

}
