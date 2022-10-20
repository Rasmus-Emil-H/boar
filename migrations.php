<?php

use app\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

$config = [
    'authenticationClass' => \app\models\User::class,
    'pdo' => [
        'dsn' => 'mysql:host=localhost;port=3306;dbname=boar', 
        'user' => 'root', 
        'password' => ''
    ]
];

$app = new Application(__DIR__, $config);

$app->database->applyMigrations();