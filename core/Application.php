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
use \app\models\SessionModel;
use \app\models\UserModel;
use \app\core\src;
use \app\core\src\miscellaneous\CoreFunctions;

final class Application {

    public string $layout = 'main';

    protected src\Router $router;
    protected src\Request $request;
    protected src\Response $response;
    protected src\Session $session;
    protected src\Cookie $cookie;
    protected Connection $connection;
    protected src\View $view;
    protected src\I18n $i18n;
    protected src\config\Config $config;
    protected src\utilities\Logger $logger;
    protected ?src\Controller $parentController;

    public static string $ROOT_DIR;
    public static self $app;
    public static $defaultRoute = ['/auth/login', '/auth/signup'];

    public function __construct(bool $applicationIsMigrating) {
        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        $this->config       = new src\config\Config();
        $this->session      = new src\Session();
        $this->request      = new src\Request();
        
        $this->setConnection();

        if ($applicationIsMigrating) return;
        
        $this->response     = new src\Response();
        $this->router       = new src\Router();
        $this->cookie       = new src\Cookie();
        $this->view         = new src\View();
        $this->logger       = new src\utilities\Logger();

        $this->getLanguage();
        $this->getUser();
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

    private function validateUserSession() {
        $session = (new SessionModel())->query()->select()->where(['Value' => $this->session->get('SessionID'), 'UserID' => $this->session->get('user')])->run();
        $validSession = !empty($session) && src\miscellaneous\CoreFunctions::first($session)->exists();
        if (!in_array($this->request->getPath(), self::$defaultRoute) && !$validSession) $this->response->redirect(src\miscellaneous\CoreFunctions::first(self::$defaultRoute)->scalar);
    }

    public function getUser() {
        $this->validateUserSession();
        $user = new UserModel();
        return $user->query()->select()->where([$user->getKeyField() => $this->session->get('user')])->run();
    }

    public function classCheck(string $class): void {
        if (class_exists($class)) return;
        $this->addSystemEvent(['Invalid class was called: ' . $class]);
        CoreFunctions::dd('Invalid class: ' . $class);
    }

    public function getParentController(): src\Controller {
        return $this->parentController;
    }

    public function setParentController(src\Controller $controller): void {
        $this->parentController = $controller;
    }

    public function addSystemEvent(array $data): void {
        (new SystemEventModel(['Data' => json_encode($data)]))->save(addMetaData: false);
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
            $this->setParentController(new \app\controllers\ErrorController($applicationError));
        }
    }

    /**
    |----------------------------------------------------------------------------
    | Static methods
    |----------------------------------------------------------------------------
    |
    */

    public static function isCLI(): bool {
        return php_sapi_name() === 'cli';     
    }

    public static function isGuest(): bool {
        return empty(self::$app->getUser());
    }

    public static function isDevSite(): bool {
        return self::$app->config->get('inDevelopment') === true;
    }

    /**
    |----------------------------------------------------------------------------
    | Protected property getters
    |----------------------------------------------------------------------------
    |
    */

    public function getConfig(): src\config\Config {
        return $this->config;
    }

    public function getConnection(): Connection {
        return $this->connection;
    }

    public function getSession(): src\Session {
        return $this->session;
    }

    public function getResponse(): src\Response {
        return $this->response;
    }

    public function getRequest(): src\Request {
        return $this->request;
    }

    public function getI18n(): src\I18n {
        return $this->i18n;
    }

    public function getView(): src\View {
        return $this->view;
    }

    public function getLogger(): src\utilities\Logger {
        return $this->logger;
    }
    
}