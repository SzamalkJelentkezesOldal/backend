<?php

use App\Models\JelentkezoTorzs;
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
        Schema::create('jelentkezo_torzs', function (Blueprint $table) {
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->primary('jelentkezo_id');
            $table->string('vezeteknev');
            $table->string('keresztnev');
            $table->string('adoazonosito')->unique()->nullable();
            $table->string('lakcim');
            $table->string('taj_szam')->unique()->nullable();
            $table->string('szuletesi_hely');
            $table->string('szuletesi_nev')->nullable();
            $table->date('szuletesi_datum');
            $table->string('allampolgarsag');
            $table->string('anyja_neve');
            $table->timestamps();
        });

        JelentkezoTorzs::create([
            'jelentkezo_id' => 1, // A megadott jelentkezo_id
            'vezeteknev' => 'Kovács',
            'keresztnev' => 'János',
            'adoazonosito' => '1234567890',
            'lakcim' => 'Budapest, Fő utca 1.',
            'taj_szam' => '987654321',
            'szuletesi_hely' => 'Debrecen',
            'szuletesi_nev' => 'Kovács János',
            'szuletesi_datum' => '2006-07-15',
            'allampolgarsag' => 'magyar',
            'anyja_neve' => 'Nagy Ilona',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jelentkezo_torzs');
    }
};
