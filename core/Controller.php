<?php

/**
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
 */

namespace app\core;

use \app\core\middlewares\Middleware;

class Controller {

    private const DEFAULT_METHOD = 'index';
    private const INVALID_VIEW = 'Invalid view';
    private const INVALID = 'Invalid';

    protected object $request;

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

    public function setRequest(object $request) {
      $this->request = $request;
    }

    protected function getRequest() {
      return $this->request;
    }

    /**
     * Get data from child
     * Then set data on instantiated controller
     * @param array [strings of to be \app\core\Controller]
     * @param \app\core\controller Parent controller
     * @return void
     */

    public function setChildData(array $childControllers): void {
      foreach ( $childControllers as $childController ) {
        [$controller, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
        $cController = '\\app\controllers\\'.$controller.'Controller';
        app()->classCheck($cController);
        $static = new $cController();
        $static->{$method}();
        app()->getController()->setData($static->getData());
        $static->execChildData();
      }
    }

    public function getView(): string {
        return $this->view ?? self::INVALID_VIEW;
    }

    protected function setView(string $dir, string $view) {
      $this->view = $this->getTemplatePath($dir, $view);
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function execChildData() {
      $this->setChildData($this->getChildren());
    }

    public function getPartialTemplate(string $partial): string {
      return $this->getTemplatePath('partials/', $partial);
    }

    public function getTemplate(string $partial): string {
      return $this->getTemplatePath('', $partial);
    }

    public function getTemplatePath(string $folder, string $template): string {
        return app()::$ROOT_DIR .  '/views/' . $folder . $template . \app\core\File::TPL_FILE_EXTENSION;
    }

    public function render(): void {
        app()->view->renderView();
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

    protected function isViewingValidEntity(string $entity) {
        $request = app()->request->getArguments();
        $entityID = getIndex($request, 2)->scalar;
        $entity = new $entity($entityID);
        if ($entityID === self::INVALID || !$entity->exists()) throw new \app\core\exceptions\NotFoundException(self::INVALID);
    }

}
