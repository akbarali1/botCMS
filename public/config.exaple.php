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
        'botToken'         => [
            "bot_username" => "BOT_TOKEN",
        ],
        'adminIds'         => [0000, 1111],
        'requiredChannels' => [
            ['id' => '@channelUsername', 'name' => 'Channel Name'],
        ],
        'adminGroupId'     => 'PrivateGroupId',
        'secretKey'        => [
            "removebg_robot" => 'SecretKey',
        ],
        'features'         => [
            'convert' => true,
            'delete'  => true,
        ],
        'debug'            => false,
    ],
];