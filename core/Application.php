<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*******************************/

namespace app\core;

use app\controllers\SiteController;
use app\controllers\AuthController;
use app\controllers\ContactController;
use app\controllers\PostController;

class Application {

    /**
     * Application requirements
     * @var resources
    */

    public static string $ROOT_DIR;

    public string $layout = 'main';
    public string $authenticationClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public Database $database;
    public ?DbModel $user;
    public View $view;
    public Authentication $authentication;

    /**
     * Application states  
     * @var states
    */

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_DELETED  = 2;

    public static Application $app;

    /**
     * Default file places  
     * @var string $uploadFolder
    */

    public const UPLOAD_FOLDER = __DIR__.'/uploads/';

    public function __construct(string $rootPath, array $pdoConfigurations) {
        
        $this->authenticationClass = $pdoConfigurations['authenticationClass'];

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        
        $this->request          = new Request();
        $this->response         = new Response();
        $this->router           = new Router($this->request, $this->response);
        $this->session          = new Session();
        $this->database         = new Database($pdoConfigurations['pdo']);
        $this->view             = new View();
        $this->authentication   = new Authentication();

        $this->authentication->checkUserBasedOnSession();

    }

    /**
     * Run the application 
     * Custom exceptions should be written inside \core\exceptions
     * @return void
    */

    public function run(): void {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->exceptionCodeHandler($e->getCode());
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('error', [
                'exception' => $e
            ]);
        }
    }

    /**
     * Exception code handler
    */

    protected function exceptionCodeHandler(mixed $code) {
        if( !is_int($code) ) 
            throw new \Exception('Invalid status code. Must be int, however ' . gettype($code) . ' is provided.');
    }

    /**
     * Getter/ Setter for controllers
     * @return Controller 
    */

    public function getController(): Controller {
        return $this->controller;
    }

    public function setController(Controller $controller): void {
        $this->controller = $controller;
    }
    
}