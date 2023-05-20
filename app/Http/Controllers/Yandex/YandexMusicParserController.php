<?php

namespace App\Http\Controllers\Yandex;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YandexMusicParserController extends Controller
{
    /**
     * Запускает парсинг треков
     */
    public function parse(Request $request)
    {
        $inputData = $request->input('inputData');
        // обработка данных
        return response()->json(['result' => $inputData]);
    }
}
