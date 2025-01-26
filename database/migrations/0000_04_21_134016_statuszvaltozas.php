<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuszvaltozas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jelentkezo_id');
            $table->unsignedBigInteger('szak_id');
            $table->foreignId('regi_allapot')->references('id')->on('allapotszotars');
            $table->foreignId('uj_allapot')->references('id')->on('allapotszotars');
            $table->timestamp('modositas_ideje')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_jelentkezes_update");

        Schema::dropIfExists('statuszvaltozas');
    }
};
