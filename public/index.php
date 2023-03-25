<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
use App\App;

session_start();
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../public/bootstrap.php';

try {
    $app = new App();
    $app->run();
} catch (Throwable $e) {
    return $e->getMessage();
}