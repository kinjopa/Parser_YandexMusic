<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('YandexArtists_Tracks', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id');
            $table->foreign('artist_id')->references('id')->on('Yandex_Artists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('YandexArtists_Tracks');
    }
};
