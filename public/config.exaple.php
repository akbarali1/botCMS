<?php

return [
    'db'       => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'database',
        'username'  => 'root',
        'password'  => 'password',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
        'timezone'  => '+00:00',
        'strict'    => true,
    ],
    'telegram' => [
        'botToken'         => 'botToken',
        'adminIds'         => [],
        'requiredChannels' => [
            ['id' => 0, 'name' => 'channel or group name'],
        ],
        'adminGroupId'     => 'adminGroupId',
        'features'         => [
            'convert' => true,
            'delete'  => true,
        ],
        'debug'            => true,
    ],
];