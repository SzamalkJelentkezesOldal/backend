<?php

use App\Models\Allapotszotar;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allapotszotars', function (Blueprint $table) {
            $table->id();
            $table->string("elnevezes");
            $table->timestamps();
        });
        Allapotszotar::create([
            "elnevezes" => "Jelentkezett"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Regisztrált"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Törzsadatok feltöltve"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Dokumentumok feltöltve"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Eldöntésre vár"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Módosításra vár"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Elfogadva"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Elutasítva"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Lezárt"
        ]);
        Allapotszotar::create([
            "elnevezes" => "Archivált"
        ]);
        Artisan::call('cache:forget allapotszotar');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allapotszotars');
    }
};
