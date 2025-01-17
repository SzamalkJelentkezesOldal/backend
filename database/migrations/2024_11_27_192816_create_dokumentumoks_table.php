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
            $table->string('adoszam_foto')->unique();
            $table->string('taj_szam_foto')->unique();
            $table->string('szemelyi_foto_elol')->unique();
            $table->string('szemelyi_foto_hatul')->unique();
            $table->string('lakcim_foto_elol')->unique();
            $table->string('erettsegi_biz')->unique();
            $table->string('tanulmanyi_fotok')->unique()->nullable();
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
