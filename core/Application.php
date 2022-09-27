<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*/

namespace app\core;

class Application {

    public static string $ROOT_DIR;

    public string $authenticationClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Controller $controller;
    public Session $session;
    public Database $database;
    public ?DbModel $user;

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
        echo $this->router->resolve();
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

}