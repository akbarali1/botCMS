<?php

use App\Service\CoreService;

if (!function_exists('info')) {
    function info($message, $context = [])
    {
        $path = __DIR__.'/../public/logs/';
        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        file_put_contents($path.date('Y-m-d').'.log', date('Y-m-d H:i:s').' '.$message.PHP_EOL, FILE_APPEND);
    }
}

if (!function_exists('config')) {
    function config($key)
    {
        $config = require __DIR__.'/../public/config.php';

        return $config[$key] ?? false;
    }
}
if (!function_exists('lang')) {
    function lang(string $key = null, $args = null): string
    {
        $defaultLang = CoreService::getLanguageCode();
        $string      = file_exists(__DIR__.'/../lang/'.$defaultLang.'.php') ? require __DIR__.'/../lang/'.$defaultLang.'.php' : require __DIR__.'/../lang/uz.php';
        $string      = $string[$key] ?? $key;
        if (isset($args)) {
            if (!is_array($args)) {
                $args = func_get_args();
                array_shift($args);
            }
            $string = vsprintf($string, $args);
        }

        return $string;
    }
}

