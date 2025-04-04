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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->foreignId('szak_id')->references('id')->on('szaks');
            $table->string('portfolio_url');
            $table->tinyInteger('allapot'); // 1 - eldöntésre vár, 2 - elfogadva, 3 - elutasítva
            $table->timestamps();
        
            // Egyedi kombinációk létrehozása (indexek)
            $table->unique(['jelentkezo_id', 'szak_id', 'portfolio_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
