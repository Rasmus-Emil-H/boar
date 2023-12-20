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

use \app\core\database\Connection;
use \app\config\Config;
use \app\controllers\AssetsController;
use \app\utilities\Logger;
use \app\models\SystemEventModel;
use \app\models\SessionModel;
use \app\models\UserModel;

class Application {

    public static string $ROOT_DIR;
    public string $layout = 'main';
    
    protected Router $router;
    protected Request $request;
    protected Response $response;
    protected Session $session;
    protected Cookie $cookie;
    protected Connection $connection;
    protected View $view;
    protected Env $env;
    protected Regex $regex;
    protected I18n $i18n;
    protected Config $config;
    protected Logger $logger;
    protected AssetsController $clientAssets;

    protected ?Controller $parentController;

    public static self $app;
    public static $defaultRoute = ['/auth/login', '/auth/signup'];

    public function __construct(bool $applicationIsMigrating) {
        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        $this->config      = new Config();
        $this->setConnection();

        if ($applicationIsMigrating) return;
        
        $this->request      = new Request();
        $this->response     = new Response();
        $this->regex        = new Regex($this->request->getPath());
        $this->router       = new Router();
        $this->session      = new Session();
        $this->cookie       = new Cookie();
        $this->view         = new View();
        $this->env          = new Env();
        $this->logger       = new Logger();
        $this->clientAssets = new AssetsController();

        $this->getLanguage();
        $this->getUser();
        $this->i18n         = new I18n();
    }

    protected function setConnection() {
        $database = $this->config->get('database');
        $applicationConfig = ['pdo' => ['dsn' => $database->dsn, 'user' => $database->user, 'password' => $database->password]];
        $this->connection = Connection::getInstance($applicationConfig['pdo']);
    }

    public function getLanguage() {
        if (!$this->session->get('language')) $this->session->set('language', self::$app->config->get('locale')->default);
    }

    public function getUser() {
        $session = (new SessionModel())::query()->select()->where(['Value' => $this->session->get('SessionID'), 'UserID' => $this->session->get('user')])->run();
        $validSession = !empty($session) && first($session)->exists();
        if (!in_array($this->request->getPath(), self::$defaultRoute) && !$validSession) $this->response->redirect(first(self::$defaultRoute)->scalar);
        $user = new UserModel();
        return $user::query()->select()->where([$user->getKeyField() => $this->session->get('user')])->run();
    }

    public function classCheck(string $class): void {
        if (class_exists($class)) return;
        $this->addSystemEvent(['Invalid class was called: ' . $class]);
        throw new \app\core\exceptions\NotFoundException('Invalid class: ' . $class);
    }

    public function getParentController(): ?Controller {
        return $this->parentController;
    }

    public function setParentController(Controller $controller): void {
        $this->parentController = $controller;
    }

    public function addSystemEvent(array $data): void {
        (new SystemEventModel())->set(['Data' => json_encode($data)])->save();
    }

    public function log(string $message, bool $exit = false): void {
        echo date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
        if ($exit) exit();
    }

    public function run(): void {
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
    | PPG
    |----------------------------------------------------------------------------
    |
    */

    public function getRegex(): Regex {
        return $this->regex;
    }

    public function getClientAssets(): AssetsController {
        return $this->clientAssets;
    }

    public function getConfig(): Config {
        return $this->config;
    }

    public function getConnection(): Connection {
        return $this->connection;
    }

    public function getSession(): Session {
        return $this->session;
    }

    public function getResponse(): Response {
        return $this->response;
    }

    public function getRequest(): Request {
        return $this->request;
    }

    public function getI18n(): I18n {
        return $this->i18n;
    }

    public function getView(): View {
        return $this->view;
    }
    
}