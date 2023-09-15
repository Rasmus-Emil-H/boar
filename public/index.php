i<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use app\core\Application;
require_once dirname(__DIR__) . '/vendor/autoload.php';
define('PASSWORD_PASSWORD_DEFAULT', 'qwd');

function dd($input) {
    echo '<pre>';
    var_dump($input);
    echo '</pre>';
    exit;
}

try {
    $app = new Application(applicationIsMigrating: false);
    $app->run();
} catch (\Throwable $e) {
    echo '<div style="background:#e63946;padding:1rem;color:#fff;box-shadow: 2px 2px 2px #000; border-radius: 2px;">
        <h3>🧙🏻‍♂️ Error 🧙🏻‍♂️</h3>
        <hr />
        <p>'.$e->getMessage() . '</p> <p> on line: ' . $e->getLine() . '</p> <p> in file: ' . $e->getFile().'</p>
        <p><pre>'.$e->getTraceAsString().'</pre></p>
    </div>';
}
