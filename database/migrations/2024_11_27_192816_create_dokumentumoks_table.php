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
        Schema::create('dokumentumoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->foreignId('dokumentum_tipus_id')->references('id')->on('dokumentum_tipuses');
            $table->json('fajlok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentumoks');
    }
};
