<?php

namespace App\Service;

use App\Models\UserModel;
use App\System\Helpers\Helper;
use App\System\View\Render;
use Mobicms\Render\Engine;

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
    public function __construct() {}

    public function home(): void
    {
        $users = UserModel::query()->limit(5)->get();
        dd($users->toArray());
    }
}