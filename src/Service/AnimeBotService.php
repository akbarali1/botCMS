<?php

namespace App\Service;

use App\Models\JpgToPdfModel;
use App\Models\UserModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Created by PhpStorm.
 * Filename: RemoveBgService.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 04/04/2023
 * Time: 10:21
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 */
class AnimeBotService extends CoreService
{
    /**
     * @throws \JsonException
     */
    public function connect(): array
    {
        $this->sendChatAction();
        if (!in_array($this->getChatId(), config('telegram')['adminIds'], true)) {
            return $this->sendMessage($this->getChatId(), 'The bot is currently not working because. Try again later');
        }

        return match ($this->getMessage()) {
            '/start' => $this->start(),
            'video'  => $this->video(),
            default  => $this->default()
        };
    }

    private function default(): array
    {

        if (filter_var($this->getMessage(), FILTER_VALIDATE_URL)) {
            $name = basename($this->getMessage());

            return $this->sendMessage($this->getChatId(), $this->getApi($name));
        }

        return $this->sendMessage($this->getChatId(), 'Nima bo`lganini tushunmadim'."\n\n Botni qayta ishga tushuring /start");
    }

    public function getApi($name)
    {
        $response = (new Client())->get('https://cdn.amediatv.uz/api/season/v2/'.$name);
        $res      = json_decode($response->getBody()->getContents(), true);
        $coll     = collect($res['seria'])->sortBy('createdAt');

        $message = '';

        $i = 0;
        foreach ($coll as $row) {
            ++$i;
            $message .= "Nomi: ".$row['name']['uz']."\n";
            $message .= "Url: <a href='".$row['url']."'>YUKLASH</a>\n\n";
            if ($i === 30) {
                $this->sendMessage($this->getChatId(), $message);
                $i       = 0;
                $message = '';
            }
        }

        return $message;
        //        info($coll);
        //        return $coll;

    }

    private function start(): array
    {
        return $this->sendMessage($this->getChatId(), "Bot ishladi");
    }

    private function video()
    {
        $caption = explode("\n", $this->request['message']['caption'] ?? 'Yo`q')[0];
        $caption .= "\n\n\n"."Kanal: @amedia_free";
        /* info(11111111111111111111111111111111111);
         info($this->request, isArray: true);
         info("File ID");
         info($this->file_id);*/

        $this->sendVideo('@amedia_free', $this->file_id, $caption);

        return $this->sendMessage($this->getChatId(), "Yuborildi.....");
    }

}

