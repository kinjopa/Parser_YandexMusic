<?php

namespace App\Parsers;

use App\Models\Yandex\Artists;
use App\Models\Yandex\Tracks;
use Illuminate\Support\Facades\Http;
use function Symfony\Component\Translation\t;

class YandexMusicParser
{
    private string $url;

    public function __construct($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('URL is missing');
        }

        if (!preg_match('#^https?://music\.yandex\.ru/artist/\d+/tracks$#', $url)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }

        $this->url = $url;
    }

    //Основной метод
    public function TranslateTracks()
    {
        $alboms_url = str_replace('tracks', 'albums', $this->url);

        // Получаем HTML страницы по URL
        $response = Http::withOptions(['http_errors' => false])->get($this->url)->throw();
        $html = $response->body();

        $response_alboms = Http::withOptions(['http_errors' => false])->get($alboms_url)->throw();
        $html_alboms = $response_alboms->body();


        // Создаём объект DOMDocument и загружаем в него полученный HTML
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        $dom_albom = new \DOMDocument();
        @$dom_albom->loadHTML($html_alboms);
        $xpath_albom = new \DOMXPath($dom_albom);
        $albumNodes = $xpath_albom->query('//div[contains(@class,"lightlist_albums")]/div[@class="lightlist__cont"]/div[@class="album album_selectable"]');
        $albumCount = $albumNodes->length;

        // Создаём объект DOMXPath для выполнения запросов XPath к документу
        $xpath = new \DOMXPath($dom);

        $artist_id =  $this->GetArtist($xpath,$albumCount);

        // Ищем все <div>-элементы, у которых атрибут class имеет значение "d-track__name" или "d-track__meta", или "d-track__info d-track__nohover"
        $nodes = $xpath->query('//div[contains(@class, "d-track__name") or contains(@class, "d-track__meta") or contains(@class, "d-track__info d-track__nohover")]');


        // Обходим найденные узлы с шагом 2 (по два одинаковых класса следуют друг за другом)
        for ($i = 0; $i < $nodes->length; $i += 3) {
            // Получаем название трека из первого узла
            $name = trim($nodes->item($i)->textContent);

            // Получаем название альбома из второго узла
            $album = trim($nodes->item($i + 1)->textContent);

            $check_track = Tracks::where('name', $name)->where('artist_id',$artist_id)->first();

            if (empty($check_track)) {
                // Получаем продолжительность трека из третьего узла
                $time = '';
                $timeNode = $nodes->item($i + 2)->getElementsByTagName('span')->item(0);

                if ($timeNode) {
                    $time = $timeNode->textContent;
                }

                // Если названия трека и альбома не пустые, добавляем их в массив треков
                if (!empty($name) && !empty($album)) {
                    $tracks = [
                        'artist_id' => $artist_id,
                        'name' => $name,
                        'album' => $album,
                        'time' => $time
                    ];
                    $track_model = new Tracks($tracks);
                    $track_model->save();
                }
            }
        }

        return true;
    }

    private function GetArtist($xpath,$albumCount)
    {

        // Ищем элемент с классом d-generic-page-head__main-top
        $mainDivs = $xpath->query('//div[@class="d-generic-page-head__main-top" or @class="d-generic-page-head__main-bottom "]');
        $mainTopDiv = $mainDivs->item(0);
        $mainBootomDiv = $mainDivs->item(1);

        // Получаем название исполнителя
        $artistTitle = $xpath->query('//h1[contains(@class, "page-artist__title")]//text()', $mainTopDiv)->item(0)->nodeValue;

        // Получаем количество слушателей за месяц
        $summaryDiv = $xpath->query('.//div[contains(@class, "page-artist__summary")]', $mainTopDiv)->item(0);
        $listenersText = $summaryDiv->getElementsByTagName('span')->item(0)->nodeValue;

        // Получаем количество добавлений в коллекцию
        $likesDiv = $xpath->query('.//span[@class="d-like d-like_theme-count"]', $mainBootomDiv)->item(0);
        $likesText = $likesDiv->getElementsByTagName('button')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;

        //Проверяем поля
        if (empty($likesText) || empty($listenersText) || empty($albumCount) || empty($artistTitle)){
            throw new \InvalidArgumentException('Invalid artists data');
        }

        //Создаем массив артиста
        $artist_data = [
            'title' => $artistTitle,
            'listeners' => $listenersText,
            'likes' => $likesText,
            'alboms' => $albumCount,
        ];

        //Проверяем есть ли такой артист
        $check_artist = Artists::where('title', $artistTitle)->first();

        if ($check_artist){
            if ($check_artist->likes !== $likesText || (int)$check_artist->alboms !== $albumCount  || $check_artist->listeners !== $listenersText){
                Artists::updateOrCreate(
                    ['title' => $artistTitle],
                    $artist_data
                );
            }

            return $check_artist->id;
        }

        $artist_model = new Artists($artist_data);
        $artist_model->save();

        return $artist_model->id;
    }
}
