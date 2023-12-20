<?php

/**
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
 */

namespace app\core;

use \app\core\middlewares\Middleware;
use \app\core\factories\ControllerFactory;

class Controller {

    private const DEFAULT_METHOD = 'index';
    private const INVALID = 'Invalid';

    protected array $data = [];
    protected array $children = [];

    protected string $view = '';

    public string $layout = 'main';
    public string $action = '';

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
     * @param \app\core\controller Parent controller
     * @return void
     */

    public function setChildData(): void {
        foreach ( $this->getChildren() as $childController ) {
            [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(['handler' => $controller]))->create();
            $cController->{$method}();
            app()->getParentController()->setData($cController->getData());
            $cController->execChildData();
        }
    }

    public function getView(): string {
        return $this->view ?? View::INVALID_VIEW;
    }

    protected function setView(string $dir, string $view) {
        $this->view = $this->getTemplatePath($dir, $view);
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function execChildData() {
        $this->setChildData();
    }

    public function getPartialTemplate(string $partial): string {
        return $this->getTemplatePath('partials/', $partial);
    }

    public function getTemplate(string $partial): string {
        return $this->getTemplatePath('', $partial);
    }

    public function getTemplatePath(string $folder, string $template): string {
        return app()::$ROOT_DIR .  File::VIEWS_FOLDER . $folder . $template . File::TPL_FILE_EXTENSION;
    }

    public function render(): void {
        app()->getView()->renderView();
    }

    public function setLayout(string $layout): void {
        $this->layout = $layout;
    }

    public function registerMiddleware(Middleware $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    protected function isViewingValidEntity(string $entity): void {
        $request = app()->getRequest()->getArguments();
        $entityID = getIndex($request, 2)->scalar;
        $entity = new $entity($entityID);
        if ($entityID === self::INVALID || !$entity->exists()) throw new \app\core\exceptions\NotFoundException(self::INVALID);
    }

}
