<?php

/*******************************
 * Bootstrap application 
 * AUTHOR: RE_WEB
 * @package app\core\application
*******************************/

namespace app\core;

class Application {

    /**
     * Application requirements
     * @var resources
    */

    public static string $ROOT_DIR;

    public string $layout = 'main';
    public string $authenticationClass;
    
    public string $trackingAPI = 'https://tracking.autologik.dk/fetch';
    public string $trackingAPIKey = 'MC45NzE3NjM1OTgwMDg4MzUy';
    
    public string $bookAPI = 'https://dev.book.autologik.dk/api';
    public string $bookAPIKey = 'MC45NzE3NjM1OTgwMDg4MzUy';
    
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public Database $database;
    public ?DbModel $user;
    public View $view;
    public Regex $regex;
    public Env $env;

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
        
        $this->request   = new Request();
        $this->response  = new Response();
        $this->regex     = new Regex();
        $this->router    = new Router($this->request, $this->response);
        $this->session   = new Session();
        $this->database  = new Database($pdoConfigurations['pdo']);
        $this->view      = new View();
        $this->env       = new Env();

        $this->checkUserBasedOnSession();

    }

    public function checkUserBasedOnSession(): void {
        $primaryValue = $this->session->get('user');
        !$primaryValue ? $this->user = null : $this->setApplicationUser($primaryValue);
    }

    public function setApplicationUser(string $primaryValue): void {
        $authenticationClass = new $this->authenticationClass();
        $primaryKey = $authenticationClass->getPrimaryKey();
        $this->user = $authenticationClass->findOne([$primaryKey => $primaryValue], $authenticationClass->tableName());
    }

    public function getLanguages() {
        $languages = new \app\models\LanguageModel();
        foreach ( $languages->getLanguages() as $languageValue ) 
            $languageSplit[$languageValue['languageID']][] = $languageValue;
        return $languageSplit??[];
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

    public function classCheck(string $class): void {
        if(!class_exists($class)) $this->response->setResponse(400, 'application/json', ['msg' => 'bad request']);
    }

    /**
     * Exception code handler
    */

    protected function exceptionCodeHandler($code) {
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

    public static function isGuest(): bool {
        return is_null(self::$app->user);
    }
    
}