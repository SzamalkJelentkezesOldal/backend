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
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->primary('jelentkezo_id');
            $table->foreignId('dokumentum_id')->references('id')->on('dokumentum_tipuses');
            $table->string('dokumentum_url')->unique();
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
