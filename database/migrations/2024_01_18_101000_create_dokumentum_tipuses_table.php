<?php

use App\Models\DokumentumTipus;
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
        Schema::create('dokumentum_tipuses', function (Blueprint $table) {
            $table->id();
            $table->string("elnevezes");
            $table->timestamps();
        });
        
        DokumentumTipus::create([
            'elnevezes' => 'Adóigazolvány',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'TAJ kártya',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Személyazonosító igazolvány első oldal',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Személyazonosító igazolvány hátsó oldal',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Lakcímet igazoló igazolvány első oldala',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Lakcímet igazoló igazolvány hátsó oldala',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Érettségi bizonyítvány',
        ]);
        
        DokumentumTipus::create([
            'elnevezes' => 'Tanulmányi dokumentumok',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentum_tipuses');
    }
};
