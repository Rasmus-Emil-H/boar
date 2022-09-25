<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function dump($arg) {
    echo '<pre>';
        var_dump($arg);
    echo '</pre>';
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\controllers\SiteController;

$app = new Application(dirname(__DIR__));

$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/about', [SiteController::class, 'about']);
$app->router->post('/about', [SiteController::class, 'handleContact']);

$app->run();