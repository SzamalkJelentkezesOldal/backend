<?php

use App\Models\Jelentkezo;
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
        Schema::create('jelentkezos', function (Blueprint $table) {
            $table->id();
            $table->string("nev");
            $table->string("email");
            $table->string("tel");
            $table->string("token");
            $table->timestamps();
        });


        Jelentkezo::create([
            "nev"=> "Teszt Felhasználó",
            "email"=>"felhasznalo@felhasznalo.com",
            "tel"=>'06202020200',
            "token"=>'aaaaaaaaaaaaaaaaaaaa',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jelentkezos');
    }
};
