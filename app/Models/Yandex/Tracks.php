<?php

namespace App\Models\Yandex;

class Tracks extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'YandexArtists_Tracks';

    protected $fillable = [
        'artist_id',
        'name',
        'album',
        'time',
    ];
}
