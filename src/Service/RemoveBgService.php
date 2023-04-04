<?php

namespace App\Service;

use App\Models\JpgToPdfModel;
use App\Models\UserModel;
use GuzzleHttp\Client;

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
class RemoveBgService extends CoreService
{
    /**
     * @throws \JsonException
     */
    public function connect(): array
    {
        $user = $this->getUser();
        if (!$user->is_premium) {
            return $this->sendMessage($this->getChatId(), 'Bu bot faqat pullik rejimda ishlaydi. Sizga Premium funksiya yoqilmagan.'."\n\n https://t.me/convertor_group da sizga bu botni yoqishlarini so`rang. Hizmatni yoqish pullik!!!\n\nChat ID: ".$this->getChatId());
        }

        if (!$this->isPrivateChat()) {
            exit('Nimadur xato');
        }

        $this->sendChatAction();
        if (config('telegram')['debug'] && !in_array($this->getChatId(), config('telegram')['adminIds'], true)) {
            return $this->sendMessage($this->getChatId(), 'The bot is currently not working because the admin is adding new things to the bot. Try again later');
        }

        return match ($this->getMessage()) {
            '/start' => $this->start(),
            'photo'  => $this->photoSave(),
            default  => $this->default()
        };
    }

    private function default(): array
    {
        return $this->sendMessage($this->getChatId(), 'Nima bo`lganini tushunmadim'."\n\n Botni qayta ishga tushuring /start");
    }

    private function start(): array
    {
        return $this->sendMessage($this->getChatId(), "Salom menga rasmni yuboring");
    }

    /**
     * @throws \JsonException
     */
    private function photoSave(): array
    {
        $userMessageSend = $this->sendMessage($this->getChatId(), "Rasmni qabul qilib oldim. Qayta ishlamoqdaman.", reply_to_message_id: $this->getMessageId());
        if (!isset($this->file_id)) {
            return $this->sendMessage($this->getChatId(), 'Fayl ID yaroqsiz', reply_to_message_id: $this->getMessageId());
        }

        $this->sendChatAction(action: 'upload_document');
        $res = $this->sendTelegram(['file_id' => $this->file_id], 'getFile');
        if ($res['ok'] === true) {
            $link = 'https://api.telegram.org/file/bot'.$this->api_key.'/'.$res['result']['file_path'];

            $fileName = $this->sendRemoveApi($link);

            return $this->sendDocument($this->getChatId(), $fileName['link'], reply_to_message_id: $userMessageSend['result']['message_id']);
        }

        return $this->sendMessage($this->getChatId(), 'Qandaydur xatolik', reply_to_message_id: $this->getMessageId());
    }

    public function getUser(): UserModel
    {
        if (is_array($this->user) && count($this->user) > 0) {
            return $this->user;
        }

        $user = UserModel::query()->where('telegram_id', '=', $this->getChatId())->first();
        if (!$user) {
            $user = UserModel::query()->create([
                'telegram_id' => $this->getChatId(),
                'condition'   => 0,
                'username'    => $this->getUsername(),
                'time'        => time(),
                'today'       => date('Y-m-d'),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        $user->update([
            'bot' => "removebg_robot",
        ]);

        $this->user = $user;

        return $this->user;
    }

    public function saveImage(array $images, int $telegramId): array
    {
        $imagick = new \Imagick($images);
        $imagick->setImageFormat('pdf');
        $path = PUBLIC_PATH.'/storage/pdf/';
        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException("PDFni yuklash uchun serverda papka yaratib bo'lmadi");
        }
        $name     = hash('sha256', time().$telegramId);
        $fileName = $path.$name.'.pdf';
        $imagick->writeImages($fileName, true);

        return [
            'path' => $fileName,
            'link' => 'https://bot.uzhackersw.uz/storage/pdf/'.$name.'.pdf',
        ];
    }

    private function sendRemoveApi($url): array
    {
        $client = new Client();
        $res    = $client->post('https://api.remove.bg/v1.0/removebg', [
            'multipart' => [
                [
                    'name'     => 'image_url',
                    'contents' => $url,
                ],
                [
                    'name'     => 'size',
                    'contents' => 'auto',
                ],
            ],
            'headers'   => [
                'X-Api-Key' => config('telegram')['secretKey']['removebg_robot'],
            ],
        ]);
        $name   = hash('sha256', time().$this->getChatId()).".png";
        $path   = PUBLIC_PATH.'/storage/removebg/'.$name;

        $fp = fopen($path, "wb");
        fwrite($fp, $res->getBody());
        fclose($fp);

        return [
            'path' => $path,
            'link' => 'https://bot.uzhackersw.uz/storage/removebg/'.$name,
        ];
    }


}

