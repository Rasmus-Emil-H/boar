<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$config = [
    'authenticationClass' => \app\models\User::class,
    'pdo' => [
        'dsn' => 'mysql:host=localhost;port=3306;dbname=boar', 
        'user' => 'root', 
        'password' => ''
    ]
];

$app = new Application(
    rootPath: dirname(__DIR__), pdoConfigurations: $config
);

$app->run();