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
class RouteService
{

    public function home()
    {
        return [
            'status'  => 'success',
            'message' => 'Hello World',
        ];
    }

    public function jpgToPdf()
    {
        return (new ConnectService())->connect();
    }
}