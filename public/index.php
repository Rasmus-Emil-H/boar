i<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;

define('PASSWORD_PASSWORD_DEFAULT', 'qwd');

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application(applicationIsMigrating: false);
$app->run();
