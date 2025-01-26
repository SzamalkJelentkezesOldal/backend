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
            $table->string('allapot', 50);
            $table->timestamp('modositas_ideje')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable(); // Opcionális
            $table->timestamps();
        });

        // Trigger létrehozása
        // DB::unprepared("
        //     CREATE TRIGGER trg_jelentkezes_update
        //     BEFORE UPDATE ON jelentkezes
        //     FOR EACH ROW
        //     BEGIN
        //         INSERT INTO statuszvaltozas (jelentkezo_id, szak_id, allapot, modositas_ideje)
        //         VALUES (OLD.jelentkezo_id, OLD.szak_id, OLD.allapot, NOW(), NULL);
        //     END
        // ");
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
