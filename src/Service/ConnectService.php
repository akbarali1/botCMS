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
        $this->sendChatAction();
        if (config('telegram')['debug'] && !in_array($this->getChatId(), config('telegram')['adminIds'], true)) {
            return $this->sendMessage($this->getChatId(), 'The bot is currently not working because the admin is adding new things to the bot. Try again later');
        }

        return match ($this->getMessage()) {
            '/start'             => $this->start(),
            '/iWillSendTheFiles' => $this->iWillSendTheFiles(),
            'stopSendMeTheFile'  => $this->stopSendMeTheFile(),
            default              => $this->default()
        };
    }

    private function default(): array
    {
        return $this->sendMessage($this->getChatId(), 'Please send me a photo');
    }

    private function start(): array
    {
        return $this->sendMessage($this->getChatId(), lang("start", $this->getFullName()));
    }

    private function iWillSendTheFiles(): array
    {
        return $this->sendMessage($this->getChatId(), lang("iWillSendTheFiles"));
    }

}