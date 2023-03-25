<?php

namespace App;

use App\Service\RouteService;

/**
 * Created by PhpStorm.
 * Filename: App.php
 * Project Name: jpgToPDF
 * Author: akbarali
 * Date: 25/03/2023
 * Time: 15:49
 * Github: https://github.com/akbarali1
 * Telegram: @akbar_aka
 * E-mail: me@akbarali.uz
 */
class App
{
    protected RouteService $routeService;

    public function __construct()
    {
        $this->routeService = new RouteService();
    }

    public function removeGetRequest($url, $which_argument = false): string
    {
        return array_filter(explode('/', preg_replace('/'.($which_argument ? '(\&|)'.$which_argument.'(\=(.*?)((?=&(?!amp\;))|$)|(.*?)\b)' : '(\?.*)').'/i', '', $url)))[1] ?? '';
    }

    public function run()
    {
      match ($this->removeGetRequest($_SERVER['REQUEST_URI'])) {
            default => $this->routeService->home(),
        };

        //header json
      /*  header('Content-Type: application/json');

        echo json_decode($response);*/
        /*  switch ($this->removeGetRequest($_SERVER['REQUEST_URI'])) {
              default:
                  $this->routeService->home();
                  break;
          }*/
    }

}