<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*******************************/

namespace app\core;
use \app\core\database\Connection;

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
    public Cookie $cookie;
    public Connection $connection;
    public View $view;
    public Env $env;
    public Regex $regex;
    public I18n $i18n;

    /**
     * Application states  
     * @var states
    */

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_DELETED  = 2;

    public static Application $app;
    public static $defaultRoute = '/auth/login';

    /**
     * Default file places  
     * @var string $uploadFolder
    */

    public const UPLOAD_FOLDER = __DIR__.'/uploads/';

    public function __construct(string $rootPath, array $pdoConfigurations) {
        
        $this->authenticationClass = $pdoConfigurations['authenticationClass'];

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        
        $this->request     = new Request();
        $this->response    = new Response();
        $this->regex       = new Regex($this->request->getPath());
        $this->router      = new Router($this->request, $this->response);
        $this->session     = new Session();
        $this->cookie      = new Cookie();
        $this->connection  = new Connection($pdoConfigurations['pdo']);
        $this->view        = new View();
        $this->env         = new Env();
        $this->i18n        = new I18n();

        $this->checkLanguage();
        $this->checkUserBasedOnSession();

    }

    public function checkLanguage() {
        if ( $this->session->get('language') === '' ) $this->session->set('language', 'Danish');
    }

    public function checkUserBasedOnSession(): void {
        $primaryValue = $this->cookie->get('sessionId');
        !$primaryValue ? $this->user = null : $this->setApplicationUser($primaryValue);
        if ( $this->user === null ) header('Location: /auth/login');
    }

    public function setApplicationUser(string $primaryValue): void {
        $authenticationClass = new $this->authenticationClass();
        $session = $authenticationClass::search(['session_id' => $primaryValue]);
        $this->user = $session[array_key_first($session)]??null;
    }

    /**
     * Run the application 
     * Custom exceptions should be written inside \core\exceptions
     * @return void
    */

    public function run(): void {
        try {
            echo $this->router->resolve();
        } catch (\Throwable $e) {
            $this->exceptionCodeHandler($e->getCode());
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('error', [
                'exception' => $e,
                'isDev' => $this->isDevSite() ?: false
            ]);
        }
    }

    public function classCheck(string $class): void {
        if(!class_exists($class)) $this->response->setResponse(400, 'application/json', ['msg' => 'bad request']);
    }

    public function globalThrower(string $message): \Exception {
        throw new \Exception($message);
    }

    /**
     * Exception code handler
     * @param code
    */

    protected function exceptionCodeHandler($code) {
        if( !is_int($code) ) 
            throw new \Exception('Invalid status code. Must be int, however ' . gettype($code) . ' is provided.');
    }

    public function isDevSite() {
        return $_SERVER['REMOTE_ADDR'] === '152.115.151.122' || $this->env->get('isDev') === 'true' || $_SERVER['REMOTE_ADDR'] === '87.62.102.71';
    }

    /**
     * Getter/ Setter for controllers
     * @return Controller 
    */

    public function getController(): Controller {
        return $this->controller;
    }

    /**
     * @param controller The desired controller
     * @return void
    */

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

    public function dd($whatever): void {
        echo '<pre>';
            var_dump($whatever);
        echo '</pre>';
        exit;
    }

    public function logout(): void {
        $this->user = null;
        $this->session->removeSessionProperty('user');
    }

    /**
     * @param value To be, translated, string
     * @return void
    */

    public function translate(string $value): string {
        return $this->i18n->translate($value);
    }

    public static function isGuest(): bool {
        return is_null(self::$app->user);
    }

    /**
     * Determine is current execution context is CLI
     * @return bool
    */

    public function isCLI(): bool {
        return php_sapi_name() === 'cli';     
    }
    
}