<?php

/**
|----------------------------------------------------------------------------
| Application router
|----------------------------------------------------------------------------
| 
| The application send the current request to this object, which dispatches
| the appropriate controller / method
|
| @author RE_WEB
| @package core
|
*/

namespace app\core\src;

use \app\core\src\factories\ControllerFactory;
use \app\core\src\miscellaneous\CoreFunctions;

final class Router {

    protected const INDEX_METHOD = 'index';
    
    protected array $path;
    protected string $method;

    public static $anonymousRoutes = ['/auth/login', '/auth/signup', '/auth/resetPassword', '/auth/twofactor', '/auth/requestNewPassword', '/auth/validateTwofactor'];

    public function __construct(
       public Request $request
    ) {
        $this->path = $request->getArguments();
    }

    protected function createController(): void {
        $app = app();
        if (empty($this->path) || $this->request->getPath() === '/') $app->getResponse()->redirect(CoreFunctions::first(self::$anonymousRoutes)->scalar);
        $handler = ucfirst(CoreFunctions::first($this->path)->scalar);
        if ($this->isResource($handler)) return;
        $controller = (new ControllerFactory(['handler' => $handler]))->create();
        $controllerMethod = $this->path[1] ?? '';
        $app->setParentController($controller);
        $this->method = $controllerMethod === '' || !method_exists($controller, $controllerMethod) ? self::INDEX_METHOD : $controllerMethod;
        if (!method_exists($controller, $this->method)) $app->getResponse()->redirect('/trip');
    }

    private function isResource(string $handler): bool {
        return str_contains(strtolower($handler), strtolower('resource'));
    }

    protected function runMiddlewares(): void {
        foreach ($this->getApplicationParentController()->getMiddlewares() as $middleware) $middleware->execute();
    }

    protected function setTemplateControllers(): void {
        if (app()::isCLI()) return;
        $this->getApplicationParentController()->setChildren(['Header', 'Footer']);
    }

    protected function runController(): void {
        $controller = $this->getApplicationParentController();
        $controller->setChildData();
        $controller->{$this->method}();
    }

    protected function hydrateDOM(): void {
        $controller = $this->getApplicationParentController();
        echo $this->handleFrontendHydration($controller, $controller->getData());
    }

    private function handleFrontendHydration(Controller $controller, array $data) {
        extract($data, EXTR_SKIP);
        $layoutFile = app()::$ROOT_DIR .  File::LAYOUTS_FOLDER . $controller->getLayout() . File::TPL_FILE_EXTENSION;
        
        ob_start();
            include_once $controller->getView();
        $viewContent = ob_get_clean();

        ob_start();
            require_once $data['header'];
            include_once $layoutFile;
            require_once $data['footer'];
        $layoutFileContent = ob_get_clean();

        return str_replace('{{content}}', $viewContent, $layoutFileContent);
    }

    private function getApplicationParentController(): Controller {
        return CoreFunctions::app()->getParentController();
    }

    public function resolve(): void {
        $this->createController();
        $this->runMiddlewares();
        $this->setTemplateControllers();
        $this->runController();
        $this->hydrateDOM();
    }

}