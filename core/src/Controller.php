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

class Controller {

    use ControllerMethodTrait;

    private const DEFAULT_METHOD = 'index';
    private const FORBIDDEN_ASSET = 'Forbidden asset type was provided';
    private const EXPECTED_ENTITY_ID_POSITION = 2;
    private const VALID_ASSET_TYPES = ['js', 'css', 'meta'];
    private const VALID_ASSET_LOCATION = ['header', 'footer'];

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
        protected Session  $session,
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
        foreach ($this->getChildren() as $childController) {
            [$handler, $method] = preg_match('/:/', $childController) ? explode(':', $childController) : [$childController, self::DEFAULT_METHOD];
            $cController = (new ControllerFactory(compact('handler')))->create();
            $cController->{$method}();

            $parentController->setData($cController->getData());
            $parentController->setChild($handler, $cController);

            $cController->setChildData();
        }
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function getChild(string $child): ?object {
        return $this->children[$child] ?? null;
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

    protected function getClientAssets() {
        return $this->clientAssets;
    }

    private function checkAssetLocation(string $type): void {
        if (in_array($type, self::VALID_ASSET_LOCATION)) return;

        throw new \app\core\src\exceptions\ForbiddenException(self::FORBIDDEN_ASSET);
    }

    private function checkAssetType(string $type): void {
        if (in_array($type, self::VALID_ASSET_TYPES)) return;

        throw new \app\core\src\exceptions\ForbiddenException(self::FORBIDDEN_ASSET);
    }    

    public function addScript(string $src) {
        return app()->getParentController()->upsertData(File::JS_EXTENSION, File::buildScript($src)); 
    }

    public function addStylesheet(string $src) {
        return app()->getParentController()->upsertData(File::CSS_EXTENSION, File::buildStylesheet($src));
    }

    public function getView(): string {
        return $this->view ?? View::INVALID_VIEW;
    }

    protected function setView(string $view, string $dir = ''): void {
        $this->view = app()->getView()->getTemplatePath($view, $dir);
    }

    public function setLayout(string $layout): void {
        $this->layout = $layout;
    }

    public function getLayout(): string {
        return $this->layout;
    }

    public function setClientLayoutStructure(string $layout, string $view, array $data = []) {
        $this->setLayout($layout);
        $this->setFrontendTemplateAndData($view, [...$data]);
    }

    public function setFrontendTemplateAndData(string $templateFile, array $data = []): self {
        $this->setData($data);
        $this->setView($templateFile);
        return $this;
    }

}