<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use \app\core\Application;
use \app\core\src\database\Migration;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application(applicationIsMigrating: true);

$migration = new Migration();
$migration->applyMigrations();
$migration->seedLanguage();