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
        Schema::create('jelentkezes', function (Blueprint $table) {
            $table->primary(['szak_id', 'jelentkezo_id']);
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->foreignId('szak_id')->references('id')->on('szaks');
            $table->string('allapot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jelentkezes');
    }
};
