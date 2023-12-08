<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;
use app\core\database\Migration;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/public/yard.php';

$app = new Application(applicationIsMigrating: true);

$migration = new Migration();
$migration->applyMigrations();
$migration->initialApplicationSeed();