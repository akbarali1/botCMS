<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Asia/Tashkent');

use App\App;

session_start();
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../public/bootstrap.php';
const PUBLIC_PATH = __DIR__;
try {
    (new App())->run();
} catch (Throwable $e) {
    dd($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString());
}