<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Created by PhpStorm.
 * Filename: CoreService.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 25/03/2023
 * Time: 20:02
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 */
class CoreService
{
    protected mixed $api_key;
    public array    $request = [];

    /**
     * @throws \JsonException
     */
    public function __construct()
    {
        $this->api_key = config('telegram')['botToken'];
        $data          = file_get_contents('php://input');
        if ($data) {
            $this->request = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }
        if (!isset($this->request['message']['chat']['id'])) {
            dd('Error: Chat ID not found');
        }
    }

    public function sendTelegram($array, $sending = 'sendMessage')
    {
        try {
            $response = (new Client(['base_uri' => 'https://api.telegram.org/bot'.$this->api_key.'/']))->post(
                $sending,
                [
                    'query' => $array,
                ]
            );
        } catch (GuzzleException $e) {
            info($e->getMessage());

            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
        try {
            return (array)json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendMessage($chat_id, $text, $parse_mode = 'HTML', $disable_notification = false, $reply_to_message_id = null, $reply_markup = null): array
    {
        if (is_array($chat_id) && count($chat_id) > 0) {
            $res = [];
            foreach ($chat_id as $item) {
                $res[] = $this->sendTelegram(
                    [
                        'chat_id'              => $item,
                        'text'                 => $text,
                        'parse_mode'           => $parse_mode,
                        'disable_notification' => $disable_notification,
                        'reply_to_message_id'  => $reply_to_message_id,
                        'reply_markup'         => $reply_markup,
                        'protect_content'      => true,
                    ]
                );
            }

            return $res;
        }

        return $this->sendTelegram(
            [
                'chat_id'              => $chat_id,
                'text'                 => $text,
                'parse_mode'           => $parse_mode,
                'disable_notification' => $disable_notification,
                'reply_to_message_id'  => $reply_to_message_id,
                'reply_markup'         => $reply_markup,
                'protect_content'      => false,
            ]
        );
    }

    public static function deleteMessage($chat_id, $message_id): array
    {
        return (new self)->sendTelegram(
            [
                'chat_id'    => $chat_id,
                'message_id' => $message_id,
            ],
            'deleteMessage'
        );
    }

    public static function editMessageText($chat_id, $message_id, $text, $parse_mode = 'HTML', $disable_web_page_preview = false, $reply_markup = null): array
    {
        return (new self)->sendTelegram(
            [
                'chat_id'                  => $chat_id,
                'message_id'               => $message_id,
                'text'                     => $text,
                'parse_mode'               => $parse_mode,
                'disable_web_page_preview' => $disable_web_page_preview,
                'reply_markup'             => $reply_markup,
            ],
            'editMessageText'
        );
    }

    public function sendChatAction(int $chat_id = 0, string $action = 'typing'): array
    {
        return $this->sendTelegram(
            [
                'chat_id' => $chat_id === 0 ? $this->getChatId() : $chat_id,
                'action'  => $action,
            ],
            'sendChatAction'
        );
    }

    public function getChatId()
    {
        $array = $this->request;

        return $array['message']['chat']['id'] ?? null;
    }

    public function getChatType()
    {
        $array = $this->request;

        return $array['message']['chat']['type'] ?? null;
    }

    //fullName
    public function getFullName(): string
    {
        $array      = $this->request;
        $last_name  = $array['message']['from']['last_name'] ?? '';
        $first_name = $array['message']['from']['first_name'] ?? '';

        return $first_name.' '.$last_name;
    }

    public function getMessageId()
    {
        $array = $this->request;

        return $array['message']['message_id'] ?? null;
    }

    public function getMessage()
    {
        $array = $this->request;

        return $array['message']['text'] ?? 414229140;
    }


    public function getForwardFromDate(): int
    {
        $array = $this->request;

        return (int)$array['message']['forward_date'];
    }

    public static function getLanguageCode(): string
    {
        $array = (new self())->request;

        return $array['message']['from']['language_code'] ?? 'uz';
    }

}