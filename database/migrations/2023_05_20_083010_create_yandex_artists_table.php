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
        Schema::create('Yandex_Artists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('listeners')->nullable();
            $table->string('likes')->nullable();
            $table->string('alboms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Yandex_Artists');
    }
};
