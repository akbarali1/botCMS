<?php
//error_reporting(E_ALL);                     // Error/Exception engine, always use E_ALL
//ini_set('ignore_repeated_errors', true); // always use TRUE
//ini_set('display_errors', false);        // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
//ini_set('log_errors', true);             // Error/Exception file logging engine.
//ini_set('error_log', __DIR__.'/../public/logs/php_errors.log'); // Logging file path
use App\App;

session_start();
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../public/bootstrap.php';

try {
    (new App())->run();
} catch (Throwable $e) {
    dd($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString());
}