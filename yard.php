<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('CRONJOB_CLI_CHECK', 'CronjobScheduler');
define('DATABASE_MIGRATION_CLI_CHECK', 'DatabaseMigration');
define('CLI_TOOL_NOT_FOUND_MESSAGE', 'CLI TOOL NOT FOUND');

define('IS_CLI', isset($argv));
