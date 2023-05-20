<?php

namespace App\Models\Yandex;

class Artists extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'Yandex_Artists';

    protected $fillable = [
        'title',
        'listeners',
        'likes',
        'alboms'
    ];
}
