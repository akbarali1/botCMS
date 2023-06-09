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
    protected int   $adminGroupId     = 0;
    public array    $request          = [];
    public array    $requiredChannels = [];
    public array    $adminIds         = [];
    public          $user             = [];
    public string   $messageQuery     = '';
    public string   $today            = '';
    public          $file_id          = '';

    /**
     * @throws \JsonException
     */
    public function __construct(string $botToken = null)
    {
        $this->api_key          = $botToken;
        $this->adminGroupId     = config('telegram')['adminGroupId'];
        $this->adminIds         = config('telegram')['adminIds'];
        $this->requiredChannels = config('telegram')['requiredChannels'];
        $data                   = file_get_contents('php://input');
        $this->file_id          = '';
        $this->messageQuery     = '';
        if ($data) {
            $this->request = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }
        $this->today = date('Y-m-d');
        //        info($this->request, isArray: true);
        //        info(json_encode($this->request));
        /*  if (!isset($this->request['message']['chat']['id'])) {
              dd('Error: Chat ID not found');
          }*/
    }

    /**
     * @throws \JsonException
     */
    public function sendTelegram($query, $method = 'sendMessage'): array
    {
        $ch = curl_init('https://api.telegram.org/bot'.$this->api_key.'/'.$method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $array = (array)json_decode($res, true, 512, JSON_THROW_ON_ERROR);
        if ($array['ok'] === false) {
            info($array['description']);
            info($method);
            info('query: '.json_encode($query));
        }

        return $array;


        try {
            $response = (new Client(['base_uri' => 'https://api.telegram.org/bot'.$this->api_key.'/']))->post(
                $method,
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

    public function sendMessage($chat_id = 0, $text, $parse_mode = 'HTML', $disable_notification = false, $reply_to_message_id = null, $reply_markup = null): array
    {
        $chat_id = ($chat_id === 0) ? $this->getChatId() : $chat_id;

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
        $chat_id = ($chat_id === 0) ? $this->getChatId() : $chat_id;

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

        return $array['callback_query']['message']['chat']['id'] ?? $array['message']['from']['id'] ?? $array['message']['chat']['id'] ?? 6210123963;
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
        $request = $this->request;

        if (isset($request['message']['photo'])) {
            $this->file_id = array_pop($this->request['message']['photo'])['file_id'];

            return 'photo';
        }

        if (isset($request['message']['video'])) {
            $this->file_id = $this->request['message']['video']['file_id'];

            return 'video';
        }

        if (isset($request['message']['document']['mime_type']) && in_array($request['message']['document']['mime_type'], ["image/jpeg", "image/png"])) {
            $this->file_id = $request['message']['document']['file_id'];

            return "photo";
        }

        $text = $request['message']['text'] ?? null;

        //check admin
        if ($this->getChatType() === 'private' && str_contains($text, '-') && in_array($this->getChatId(), $this->adminIds, true)) {
            $arr                = array_map('trim', explode('-', $text));
            $text               = $arr[0];
            $this->messageQuery = $arr[1] ?? null;
        }

        return $text;

    }


    public function getForwardFromDate(): int
    {
        $array = $this->request;

        return (int)$array['message']['forward_date'];
    }

    public static function getLanguageCode(): string
    {
        $array = (new self())->request;

        return $array['message']['from']['language_code'] ?? 'en';
    }

    public function checkPhoto(): string|bool
    {
        $request = $this->request;

        return isset($request['message']['photo']) ? 'photo' : false;
    }

    public function getUsername()
    {
        $request = $this->request;

        return $request['message']['from']['username'] ?? 'nousername';
    }

    public function sendDocument($chat_id, $document, $caption = null, $parse_mode = null, $disable_notification = false, $reply_to_message_id = null, $reply_markup = null): array
    {
        //        info($document);
        //        info(curl_file_create($document));

        return $this->sendTelegram(
            [
                'chat_id'              => $chat_id,
                'document'             => $document,
                'caption'              => $caption,
                'parse_mode'           => $parse_mode,
                'disable_notification' => $disable_notification,
                'reply_to_message_id'  => $reply_to_message_id,
                'reply_markup'         => $reply_markup,
            ],
            'sendDocument'
        );
    }

    public function getChatMember(mixed $channel, int $userId)
    {
        return $this->sendTelegram(
            [
                'chat_id' => $channel,
                'user_id' => $userId,
            ],
            'getChatMember'
        );
    }

    /**
     * @throws \JsonException
     */
    public function restrictChatMember(int $chat_id, int $userId, int $until_date, $can_send_messages = true): array
    {
        return $this->sendTelegram(
            [
                'chat_id'         => $chat_id,
                'user_id'         => $userId,
                'until_date'      => $until_date,
                'chatPermissions' => [
                    'can_send_messages' => $can_send_messages,
                ],
            ],
            'restrictChatMember'
        );
    }

    public function isPrivateChat(): bool
    {
        return $this->getChatType() === 'private';
    }

    public function isGroup(): bool
    {
        return isset($this->request['message']['chat']['type']) && $this->request['message']['chat']['type'] === 'supergroup';
    }


    public function sendVideo($chat_id, $video, $caption = null, $parse_mode = null, $disable_notification = false, $reply_to_message_id = null, $reply_markup = null): array
    {
        return $this->sendTelegram(
            [
                'chat_id'              => $chat_id,
                'video'                => $video,
                'caption'              => $caption,
                'parse_mode'           => $parse_mode,
                'disable_notification' => $disable_notification,
                'reply_to_message_id'  => $reply_to_message_id,
                'reply_markup'         => $reply_markup,
                'protect_content'      => true,
            ],
            'sendVideo'
        );
    }


}