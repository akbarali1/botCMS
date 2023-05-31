<?php

namespace App\Service;


use JetBrains\PhpStorm\NoReturn;

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
class RouteService
{
    public function home()
    {
        return [
            'status'  => 'success',
            'message' => 'Hello World',
        ];
    }

    /**
     * @throws \JsonException
     */
    public function jpgToPdf(): array
    {
        return (new JpgToPdfService(config('telegram')['botToken']['jpgtopdfrobot']))->connect();
    }

    public function jpgToPdfTest()
    {
        JpgToPdfService::test();
    }

    #[NoReturn] public function storage(): void
    {
        $file = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
        if (!file_exists($file)) {
            //not found status
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($file));
        flush(); // Flush system output buffer
        readfile($file);
        exit;
    }

    /**
     * @throws \JsonException
     */
    public function removeBgRobot(): array
    {
        return (new RemoveBgService(config('telegram')['botToken']['removebg_robot']))->connect();
    }

    /**
     * @throws \JsonException
     */
    public function animeBot(): array
    {
        return (new AnimeBotService(config('telegram')['botToken']['anime']))->connect();
    }

    public function test()
    {
        $array = [];
        $files = glob('/var/www/bot.uzhackersw.uz/public/storage/images/414229140/11/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file) && mime_content_type($file) === 'image/jpeg') {
                $array[] = $file;
            }
        }
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        die;
    }

}