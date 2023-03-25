<?php

namespace App\Service;

/**
 * Created by PhpStorm.
 * Filename: RouteService.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 25/03/2023
 * Time: 15:59
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 */
class ConnectService extends CoreService
{
    public function connect(): array
    {
        return $this->sendTelegram([
            'chat_id' => config('telegram')['chatId'],
            'text'    => 'Hello World',
        ]);
    }
}