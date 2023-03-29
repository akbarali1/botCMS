<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 0);
//error_reporting(E_ALL & ~E_NOTICE);
//date_default_timezone_set('Asia/Tashkent');

use App\App;
use App\Service\CoreService;

session_start();
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../public/bootstrap.php';
const PUBLIC_PATH = __DIR__;
try {
    (new App())->run();
} catch (Throwable|Exception $e) {
    $coreService = new CoreService();
    if (in_array($coreService->getChatId(), config('telegram')['adminIds'], true)) {
        $res = $coreService->sendMessage($coreService->getChatId(), '<code>'.json_encode([$e->getMessage(), $e->getTraceAsString(), ...$e->getTrace()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT).'</code>', ['parse_mode' => 'HTML']);
    }
    //header json
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode($res ?? ['status' => 'error', 'message' => $e->getMessage()]);
}