<?php

/**
|----------------------------------------------------------------------------
| Bootstrap application
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/

namespace app\core;

use \app\core\src\database\Connection;
use \app\models\SystemEventModel;
use \app\models\UserModel;
use \app\core\src\miscellaneous\CoreFunctions;

use \app\core\src\traits\ApplicationGetterTrait;
use \app\core\src\traits\ApplicationStaticMethodTrait;

final class Application {

    use ApplicationGetterTrait;
    use ApplicationStaticMethodTrait;

    protected src\Router $router;
    protected src\Request $request;
    protected src\Response $response;
    protected src\Session $session;
    protected Connection $connection;
    protected src\View $view;
    protected src\I18n $i18n;
    protected src\config\Config $config;
    protected src\utilities\Logger $logger;
    protected src\Controller $parentController;

    public static string $ROOT_DIR;
    public static self $app;
    public function __construct() {

        new src\WebApplicationFirewall();

        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        $this->config       = new src\config\Config();
        $this->session      = new src\Session();
        $this->request      = new src\Request($this);
        
        $this->setConnection();

        $this->response     = new src\Response();
        $this->router       = new src\Router($this->getRequest());
        $this->view         = new src\View();
        $this->logger       = new src\utilities\Logger();

        $this->getLanguage();
        $this->validateUserSession();
        $this->i18n         = new src\I18n();
    }

    protected function setConnection() {
        $database = $this->config->get('database');
        $applicationConfig = ['pdo' => ['dsn' => $database->dsn, 'user' => $database->user, 'password' => $database->password]];
        $this->connection = Connection::getInstance($applicationConfig['pdo']);
    }

    public function getLanguage() {
        if (!$this->session->get('language')) $this->session->set('language', self::$app->config->get('locale')->default);
    }

    public function setLanguage(string $language): void {
        $cLanguage = new \app\models\LanguageModel();
        $search = $cLanguage->search(['Code' => $language]);

        if (empty($search)) $this->response->notFound();

        $this->session->set('language', $language);
    }

    private function validateUserSession() {
        if (str_contains($this->request->getPath(), 'admin')) return;
        $validSession = (new UserModel())->hasActiveSession();
        if (!in_array($this->request->getPath(), $this->router::$anonymousRoutes) && !$validSession) 
            $this->response->redirect(CoreFunctions::first($this->router::$anonymousRoutes)->scalar);
    }

    public function classCheck(string $class): void {
        if (class_exists($class)) return;
        $this->addSystemEvent(['Invalid class was called: ' . $class . json_encode(debug_backtrace())]);
        if (!self::isDevSite()) $this->getResponse()->notFound();
        CoreFunctions::dd('Invalid class: ' . $class);
    }

    public function getParentController(): src\Controller {
        return $this->parentController;
    }

    public function setParentController(src\Controller $controller): void {
        $this->parentController = $controller;
    }

    public function addSystemEvent(array|string $data): void {
        (new SystemEventModel(['Data' => is_string($data) ? $data : json_encode($data)]))->save(addMetaData: false);
    }

    public function log(string $message, bool $exit = false): void {
        echo date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
        if ($exit) exit();
    }

    public function bootstrap(): void {
        try {
            $this->router->resolve();
        } catch (\Throwable $applicationError) {
            $this->logger->log($applicationError);
            if ($this->isDevSite()) CoreFunctions::d($applicationError);
            CoreFunctions::dd('Application error');
        }
    }
    
}
