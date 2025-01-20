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
            $table->boolean('nappali');
            $table->timestamps();
        });

        Szak::create([
            'elnevezes' => 'Informatikai rendszer- és alkalmazás-üzemeltető technikus',
            'portfolio' => false,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Szoftverfejlesztő és tesztelő',
            'portfolio' => false,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Dekoratőr',
            'portfolio' => false,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Divat-, jelmez- és díszlettervező (Divattervező)',
            'portfolio' => false,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Fotográfus (Kreatív fotográfus)',
            'portfolio' => true,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Grafikus',
            'portfolio' => true,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Mozgókép- és animációkészítő',
            'portfolio' => true,
            'nappali' => true
        ]);
        Szak::create([
            'elnevezes' => 'Informatikai rendszer- és alkalmazás-üzemeltető technikus',
            'portfolio' => false,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'Szoftverfejlesztő és tesztelő',
            'portfolio' => false,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'Dekoratőr',
            'portfolio' => false,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'Divat-, jelmez- és díszlettervező (Divattervező)',
            'portfolio' => false,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'Fotográfus (Kreatív fotográfus)',
            'portfolio' => true,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'Grafikus',
            'portfolio' => true,
            'nappali' => false
        ]);
        Szak::create([
            'elnevezes' => 'E | Mozgókép- és animációkészítő',
            'portfolio' => true,
            'nappali' => false
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
