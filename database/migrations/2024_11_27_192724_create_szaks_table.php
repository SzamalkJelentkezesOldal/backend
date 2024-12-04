<?php

use App\Models\Szak;
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
        Schema::create('szaks', function (Blueprint $table) {
            $table->id();
            $table->string("elnevezes");
            $table->boolean("portfolio");
            $table->timestamps();
        });

        Szak::create([
            'elnevezes' => 'N | Informatikai rendszer- és alkalmazás-üzemeltető technikus',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'N | Szoftverfejlesztő és tesztelő',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'N | Dekoratőr',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'N | Divat-, jelmez- és díszlettervező (Divattervező)',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'N | Fotográfus (Kreatív fotográfus)',
            'portfolio' => true
        ]);
        Szak::create([
            'elnevezes' => 'N | Grafikus',
            'portfolio' => true
        ]);
        Szak::create([
            'elnevezes' => 'N | Mozgókép- és animációkészítő',
            'portfolio' => true
        ]);
        Szak::create([
            'elnevezes' => 'E | Informatikai rendszer- és alkalmazás-üzemeltető technikus',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'E | Szoftverfejlesztő és tesztelő',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'E | Dekoratőr',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'E | Divat-, jelmez- és díszlettervező (Divattervező)',
            'portfolio' => false
        ]);
        Szak::create([
            'elnevezes' => 'E | Fotográfus (Kreatív fotográfus)',
            'portfolio' => true
        ]);
        Szak::create([
            'elnevezes' => 'E | Grafikus',
            'portfolio' => true
        ]);
        Szak::create([
            'elnevezes' => 'E | Mozgókép- és animációkészítő',
            'portfolio' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('szaks');
    }
};
