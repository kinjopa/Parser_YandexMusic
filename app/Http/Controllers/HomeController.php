<?php

namespace App\Http\Controllers;

use App\Parsers\YandexMusicParser;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->middleware('auth');*/
    }

    /**
     * Запуск парсинга по url
     *
     */
    public function index()
    {
        //Url
        $url = 'https://music.yandex.ru/artist/79215/tracks';

        $parser = new YandexMusicParser($url);
        $result = $parser->TranslateTracks();

        if ($result){
            echo 'Парсинг завершен успешно.';
        }
    }

}
