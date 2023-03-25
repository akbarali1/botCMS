<?php

class Config
{
    public static function get(): array
    {
        return [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'db_name',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'timezone'  => '+00:00',
            'strict'    => true,
        ];

    }
}

return Config::get();