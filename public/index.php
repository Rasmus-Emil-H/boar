<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'authenticationClass' => \app\models\User::class,
    'pdo' => ['dsn' => $_ENV['DB_DSN'], 'user' => $_ENV['DB_USER'], 'password' => $_ENV['DB_PASSWORD']]
];

$app = new Application(dirname(__DIR__), $config);
$app->run();