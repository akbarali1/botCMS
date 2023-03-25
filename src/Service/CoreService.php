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

    public function __construct()
    {
        $this->api_key = config('telegram')['botToken'];
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
                'protect_content'      => true,
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

    public function sendChatAction(int $chat_id, string $action = 'typing'): array
    {
        return $this->sendTelegram(
            [
                'chat_id' => $chat_id,
                'action'  => $action,
            ],
            'sendChatAction'
        );
    }

    public function getChatId(array $array)
    {
        return $array['message']['chat']['id'] ?? null;
    }

    public function getMessageId(array $array)
    {
        return $array['message']['message_id'] ?? null;
    }

    public function getMessage(array $array)
    {
        return $array['message']['text'] ?? 414229140;
    }


    public function getForwardFromDate(array $array): int
    {
        return (int)$array['message']['forward_date'];
    }

}