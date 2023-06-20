<?php

use app\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

$config = [
    'authenticationClass' => \app\models\UserModel::class,
    'pdo' => [
        'dsn' => 'mysql:host=localhost;port=8889;dbname=boar', 
        'user' => 'root', 
        'password' => 'root'
    ]
];

$app = new Application(__DIR__, $config, true);

$app->connection->applyMigrations();