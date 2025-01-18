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
        Schema::create('jelentkezo_torzs', function (Blueprint $table) {
            $table->foreignId('jelentkezo_id')->references('id')->on('jelentkezos');
            $table->primary('jelentkezo_id');
            $table->string('vezeteknev');
            $table->string('keresztnev');
            $table->string('adoazonosito')->unique();
            $table->string('lakcim');
            $table->string('taj_szam')->unique();
            $table->string('anyja_neve');
            $table->string('szuletesi_hely');
            $table->string('szuletesi_nev');
            $table->date('szuletesi_datum');
            $table->string('allampolgarsag');
            $table->string('allampolgarsag');
            $table->string('szulo_elerhetoseg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jelentkezo_torzs');
    }
};
