<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*******************************/

namespace app\core;

use \app\core\database\Connection;
use \app\config\Config;

class Application {

    public static string $ROOT_DIR;
    public string $layout = 'main';
    public string $authenticationClass;
    
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public Cookie $cookie;
    public Connection $connection;
    public View $view;
    public Env $env;
    public Regex $regex;
    public I18n $i18n;
    public Config $config;

    public static self $app;
    public static $defaultRoute = '/auth/login';

    /**
     * Default file places  
     * @var string $uploadFolder
    */

    public const UPLOAD_FOLDER = __DIR__.'/uploads/';

    public function __construct(bool $applicationIsMigrating) {
        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        $this->config      = new Config();
        $this->setupConnection();

        if ( $applicationIsMigrating ) return;
        
        $this->request     = new Request();
        $this->response    = new Response();
        $this->regex       = new Regex($this->request->getPath());
        $this->router      = new Router($this->request, $this->response);
        $this->session     = new Session();
        $this->cookie      = new Cookie();
        $this->view        = new View();
        $this->env         = new Env();
        $this->i18n        = new I18n();

        $this->checkLanguage();
        $this->checkUserBasedOnSession();

    }

    protected function setupConnection() {
        $applicationConfig = [
            'authenticationClass' => \app\models\UserModel::class,
            'pdo' => [
                'dsn' => $this->config->get('database')->dsn, 
                'user' => $this->config->get('database')->user, 
                'password' => $this->config->get('database')->password
            ]
        ];

        $this->authenticationClass = $applicationConfig['authenticationClass'];
        $this->connection  = new Connection($applicationConfig['pdo']);
    }

    public function checkLanguage() {
        if ( $this->session->get('language') === '' ) $this->session->set('language', 'Danish');
    }

    public function checkUserBasedOnSession(): void {
        $this->session->get('user') ? null : $this->setApplicationUser();
    }

    public function setApplicationUser(): void {
        
    }

    /**
     * Run the application 
     * Custom exceptions should be written inside \core\exceptions
     * @return void
    */

    public function run(): void {
        try {
            $this->router->resolve();
        } catch (\Throwable $applicationError) {
            var_dump($applicationError);
            $this->setController(new \app\controllers\ErrorController($applicationError));
        }
    }

    public function classCheck(string $class): void {
        if(!class_exists($class)) $this->response->setResponse(400, 'application/json', ['msg' => 'bad request']);
    }

    public function globalThrower(string $message): \Exception {
        throw new \Exception($message);
    }

    protected function exceptionCodeHandler($code) {
        if( !is_int($code) ) 
            throw new \Exception('Invalid status code. Must be int, however ' . gettype($code) . ' is provided.');
    }

    public function isDevSite(): bool {
        return in_array($_SERVER['REMOTE_ADDR'], self::$app->config->get('env')->developmentArrayIPs) || $this->env->get('isDev') === 'true';
    }

    public function getController(): Controller {
        return $this->controller;
    }

    public function setController(Controller $controller): void {
        $this->controller = $controller;
    }

    /**
     * @param mixed $data
     * Die and dump your data
     * @return void
    */

    public function dd(mixed $data): void {
        echo '<pre>';
            var_dump($target);
        echo '</pre>';
        exit;
    }

    public function isCLI(): bool {
        return php_sapi_name() === 'cli';     
    }

    public static function isGuest(): bool {
        return is_null(Application::$app->session->get('user')) || self::$app->session->get('user') === '';
    }

    public function logout(): void {
        $this->session->unset('user');
    }
    
}
