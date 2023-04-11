<?php

namespace App;

use App\Service\RouteService;
use JetBrains\PhpStorm\NoReturn;

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

    /**
     * @throws \JsonException
     */
    #[NoReturn] public function run(): void
    {
      /*if (pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) === '/storage/pdf') {
           // $this->routeService->storage();
        }*/
        $res = match (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) {
            '/jpgToPdf'      => $this->routeService->jpgToPdf(),
            '/removeBgRobot' => $this->routeService->removeBgRobot(),
            #'/jpgToPdf/test' => $this->routeService->jpgToPdfTest(),
            default          => $this->routeService->home(),
        };
        header('Content-Type: application/json');
        try {
            echo json_encode($res, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\JsonException $e) {
            dd($e->getMessage());
        }

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