<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'yard.php';

try {
    $app = new Application(applicationIsMigrating: false);
    $app->bootstrap();
} catch (\Throwable $e) {
    dd($e->getMessage());
}