<?php

namespace App\Service;

use App\Models\JpgToPdfModel;
use App\Models\UserModel;

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
        $user = $this->getUser();
        if ($user->is_ban) {
            return $this->sendMessage($this->getChatId(), lang("banned"));
        }

        if (!$this->isPrivateChat()) {
            exit();
        }

        $this->sendChatAction();
        if (config('telegram')['debug'] && !in_array($this->getChatId(), config('telegram')['adminIds'], true)) {
            return $this->sendMessage($this->getChatId(), 'The bot is currently not working because the admin is adding new things to the bot. Try again later');
        }
        if ($this->checkChannelJoin()) {
            $message = lang("channelJoinError")."\n\n";
            foreach ($this->requiredChannels as $key => $channel) {
                $message .= ($key + 1).") ".$channel['id']." - <a href='https://t.me/".$channel['id']."'>".$channel['name']."</a>\n";
            }

            return $this->sendMessage($this->getChatId(), $message);
        }

        $res = match ($this->getMessage()) {
            '/start'             => $this->start(),
            '/iWillSendTheFiles' => $this->iWillSendTheFiles(),
            'photo'              => $this->photoSave(),
            '/stopSendMeTheFile' => $this->stopSendMeTheFile(),
            default              => $this->default()
        };

        return $res;
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
        $user = $this->getUser();
        if ($user->condition === 0) {
            $user->condition = 1;
            $user->save();

            return $this->sendMessage($this->getChatId(), lang("iWillSendTheFiles"));
        }

        if ($user->condition === 1) {
            return $this->sendMessage($this->getChatId(), lang("iWillSendTheFilesError"));
        }

        return $this->sendMessage($this->getChatId(), lang("toRememberError").' '.$this->getChatId());
    }

    private function photoSave()
    {
        $user = $this->getUser();
        if ($user->condition === 0) {
            return $this->sendMessage($this->getChatId(), lang("photoSaveError"), reply_to_message_id: $this->getMessageId());
        }

        $photo = array_pop($this->request['message']['photo']);
        $res   = $this->sendTelegram(['file_id' => $photo['file_id']], 'getFile');
        if ($res['ok'] === true) {
            $user = $this->getUser();
            $link = 'https://api.telegram.org/file/bot'.$this->api_key.'/'.$res['result']['file_path'];
            JpgToPdfModel::query()->create([
                'user_id' => $user->id,
                'link'    => $link,
            ]);

            return $this->sendMessage($this->getChatId(), lang("photoSave"), reply_to_message_id: $this->getMessageId());
        }

        return $this->sendMessage($this->getChatId(), 'Photo saved', reply_to_message_id: $this->getMessageId());
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

        $this->user = $user;

        return $this->user;
    }

    public static function test()
    {
        /*     $chatId   = config('telegram')['adminIds'][0];
             $user     = UserModel::query()->with(['jpgToPdfNoActive', 'jpgToPdf'])->where('telegram_id', '=', $chatId)->first();
             $fileLink = $user->jpgToPdfNoActive->pluck('link')->toArray();
             $imagick  = new \Imagick($fileLink);
             $imagick->setImageFormat('pdf');
             $path = PUBLIC_PATH.'/storage/pdf/';
             if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
                 throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
             }
             $imagick->writeImages($path.$user->telegram_id.'.pdf', true);

             dd($imagick);
             dd($fileLink);*/
    }

    public function stopSendMeTheFile(): array
    {
        $user     = $this->getUser();
        $fileLink = $user->jpgToPdfNoActive->pluck('link')->toArray();
        if (count($fileLink) === 0) {
            return $this->sendMessage($this->getChatId(), lang("noFileSend"), reply_to_message_id: $this->getMessageId());
        }

        $userMessageSend = $this->sendMessage($this->getChatId(), lang("jptToPdfConvertPending"), reply_to_message_id: $this->getMessageId());
        $this->sendChatAction($this->adminGroupId);
        $adminSendMessage = $this->sendMessage($this->adminGroupId, lang("adminGroupSend", [$this->getFullName(), $this->getUsername(), $this->getChatId()]));

        $fileName = $this->saveImage($fileLink, $user->telegram_id);

        JpgToPdfModel::query()->where('user_id', '=', $user->id)->update(['status' => 1]);
        $user->condition = 0;
        $user->save();

        $this->sendChatAction(action: 'upload_document');
        $this->sendChatAction($this->adminGroupId, 'upload_document');
        $this->sendDocument($this->adminGroupId, $fileName['link'], reply_to_message_id: $adminSendMessage['result']['message_id']);

        $response = $this->sendDocument($this->getChatId(), $fileName['link'], reply_to_message_id: $userMessageSend['result']['message_id']);
        unlink($fileName['path']);

        return $response;
    }

    public function saveImage(array $images, int $telegramId): array
    {
        $imagick = new \Imagick($images);
        $imagick->setImageFormat('pdf');
        $path = PUBLIC_PATH.'/storage/pdf/';
        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException("PDFni yuklash uchun serverda papka yaratib bo'lmadi");
        }
        $fileName = $path.$telegramId.'.pdf';
        $imagick->writeImages($fileName, true);

        return [
            'path' => $fileName,
            'link' => 'https://bot.uzhackersw.uz/storage/pdf/'.$telegramId.'.pdf',
        ];
    }

    public function checkChannelJoin(): bool
    {
        if (count($this->requiredChannels) === 0) {
            return false;
        }
        foreach ($this->requiredChannels as $channel) {
            $chatMember = $this->getChatMember($channel['id'], $this->getChatId());
            if (($chatMember['ok'] === true) && $chatMember['result']['status'] === 'left') {

                return true;
            }
        }

        return false;

    }

    /**
     * @throws \JsonException
     */
    private function removeJoinChannel(): false|array
    {
        $request = $this->request;
        //        info(json_encode($request, JSON_THROW_ON_ERROR));
        $fromId = $this->getChatId();
        $chatId = $request['message']['chat']['id'];
        if (!$fromId) {
            return false;
        }
        $user = $this->getUser();

        if (!empty($data['message']['left_chat_member'])) {
            self::deleteMessage($chatId, $this->getMessageId());
            if ($user->is_ban) {
                return $this->sendMessage($fromId, lang("youAreBannedAndLeft"));
            }
            $user->is_ban = 1;
            $user->save();

            $this->restrictChatMember($chatId, $fromId, 60 * 60 * 24 * 365, false);

            return $this->sendMessage($fromId, lang("youAreBanned"));
        }

        if (!empty($data['message']['new_chat_member'])) {
            if ($user->is_ban) {
                $this->restrictChatMember($chatId, $fromId, 60 * 60 * 24 * 365, false);

                return $this->sendMessage($fromId, lang("youAreBannedAndLeft"));
            }
            self::deleteMessage($chatId, $this->getMessageId());
        }

        if ($user->is_ban) {
            $this->restrictChatMember($chatId, $user->telegram_id, 60 * 60 * 24 * 365, false);

            return $this->sendMessage($fromId, lang("banned"));
        }

        return false;
    }

}

