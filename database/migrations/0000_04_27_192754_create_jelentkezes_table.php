<?php

use App\Models\Jelentkezes;
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
            $table->id();
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->foreignId('szak_id')->references('id')->on('szaks');
            $table->foreignId('allapot')->references('id')->on('allapotszotars');
            $table->tinyInteger('sorrend')->default(0);
            $table->boolean('lezart')->default(false);
            $table->timestamps();


            $table->unique(['jelentkezo_id', 'szak_id']);
        });
        
        /*Jelentkezes::create([
            "szak_id"=>1,
            "jelentkezo_id"=>1,
            "allapot"=>2
        ]);
        Jelentkezes::create([
            "szak_id"=>9,
            "jelentkezo_id"=>1,
            "allapot"=>2
        ]);*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jelentkezes');
    }
};
