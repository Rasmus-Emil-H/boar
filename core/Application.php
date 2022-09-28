<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*/

namespace app\core;

use app\controllers\SiteController;
use app\controllers\AuthController;
use app\controllers\ContactController;

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

    public static Application $app;

    public function __construct(string $rootPath, array $pdoConfigurations) {
        
        $this->authenticationClass = $pdoConfigurations['authenticationClass'];

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->database = new Database($pdoConfigurations['pdo']);
        $this->view = new View();

        $primaryValue = $this->session->get('user');

        if ($primaryValue) {            
            $authenticationClass = new $this->authenticationClass();
            $primaryKey = $authenticationClass->getPrimaryKey();
            $this->user = $authenticationClass->findOne([$primaryKey => $primaryValue], $authenticationClass->tableName());
        } else {
            $this->user = null;
        }

    }

    public function run() {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('error', [
                'exception' => $e
            ]);
        }
    }

    public function getController(): Controller {
        return $this->controller;
    }

    public function setController(Controller $controller): void {
        $this->controller = $controller;
    }

    public function login(DbModel $user): bool {
        $this->user = $user;
        $primaryKey = $user->getPrimaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(): void {
        $this->user = null;
        $this->session->removeSessionProperty('user');
    }

    public static function isGuest(): bool {
        return is_null(self::$app->user);
    }

    public function registerRoutes(): void {
        $this->router->get('/', [SiteController::class, 'home']);
        $this->router->get('/about', [SiteController::class, 'about']);
        $this->router->post('/about', [SiteController::class, 'handleContact']);

        $this->router->get('/login', [AuthController::class, 'login']);
        $this->router->post('/login', [AuthController::class, 'login']);

        $this->router->get('/register', [AuthController::class, 'register']);
        $this->router->post('/register', [AuthController::class, 'register']);

        $this->router->get('/logout', [AuthController::class, 'logout']);

        $this->router->get('/profile', [AuthController::class, 'profile']);

        $this->router->get('/ticket', [ContactController::class, 'ticket']);
        $this->router->post('/ticket', [ContactController::class, 'ticket']);
    }

    public function dump($argv) {
        echo '<pre>';
            var_dump($argv);
        echo '</pre>';
        exit();
    }
    
}