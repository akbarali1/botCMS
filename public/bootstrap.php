<?php
require __DIR__.'/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$config  = require __DIR__.'/../public/config.php';
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $config['driver'] ?? 'mysql',
    'host'      => $config['host'] ?? 'localhost',
    'database'  => $config['database'] ?? 'database',
    'username'  => $config['username'] ?? 'root',
    'password'  => $config['password'] ?? '',
    'charset'   => $config['charset'] ?? 'utf8mb4',
    'collation' => $config['collation'] ?? 'utf8mb4_unicode_ci',
    'prefix'    => $config['prefix'] ?? '',
    'timezone'  => $config['timezone'] ?? '+00:00',
    'strict'    => $config['strict'] ?? true,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule->getDatabaseManager()->getPdo();

