<?php

use App\Http\Controllers\DokumentumokController;
use App\Http\Controllers\DokumentumTipusController;
use App\Http\Controllers\JelentkezesController;
use App\Http\Controllers\JelentkezoController;
use App\Http\Controllers\JelentkezoTorzsController;
use App\Http\Controllers\SzakController;
use App\Http\Controllers\UgyintezoController;
use App\Http\Middleware\Master;
use App\Http\Middleware\Ugyintezo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Általános
Route::get('/szakok', [SzakController::class, 'getSzakok']);
Route::get('/dokumentum-tipusok', [DokumentumTipusController::class, 'getDokumentumTipusok']);
Route::post("/uj-jelentkezo", [JelentkezoController::class, 'postJelentkezoJelentkezesPortfolio']);
Route::get('/szakok-szama/{id}', [JelentkezesController::class, 'countJelentkezesSzama']);
Route::get('/jelentkezo-adatai/{email}', [JelentkezoTorzsController::class, 'getJelentkezoAdatok']);




// Jelentkező
Route::middleware(['auth:sanctum'])
    ->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // jelentkező törzsadatának feltöltése
    Route::post('/torzsadat-feltolt', [JelentkezoTorzsController::class, 'torzsadatFeltoltes']);

    // jelentkező dokumentjainak feltöltése
    Route::post('/dokumentumok-feltolt', [DokumentumokController::class, 'dokumentumokFeltolt']);

    // jelentkezési nyilatkozat letöltése év szerint
    Route::get('/nyilatkozat-letoltes/{year}', [DokumentumokController::class, 'nyilatkozatLetolt']);

    // egy jelentkezőnek a szakokra való jelentkezését listázza
    Route::get('/jelentkezesek/{jelentkezo}', [JelentkezesController::class, 'getJelentkezesek']);

    // egy jelentkezőnek a szakokra való jelentkezésének sorrendjét módosítja/beiratkozik
    Route::patch('/jelentkezesek/sorrend/{jelentkezo}/{beiratkozik}', [JelentkezesController::class, 'updateSorrend']);

    // egy jelentkezőnek a jelenlegi státuszát kéri le
    Route::get('/jelentkezes-allapot/{email}', [JelentkezesController::class, 'getJelentkezesAllapot']);

    // egy jelentkező a törzsadatát módosítja
    Route::patch('/torzsadat-frissit/{jelentkezo_id}', [JelentkezoTorzsController::class, 'updateJelentkezoTorzs']);

    // egy jelentkezőnek a kitöltött dokumentait kapja meg
    Route::get('/dokumentumok', [DokumentumokController::class, 'getDokumentumok']);

    // egy jelentkezőnek egy adott dokumentumját törli
    Route::delete('/dokumentumok', [DokumentumokController::class, 'deleteDokumentum']);

    // file preview
    Route::get('/dokumentumok/preview', [DokumentumokController::class, 'previewDokumentum']);

});


Route::get("/jelentkezok", [JelentkezoController::class, 'index']);

// Ügyintéző 
Route::middleware(['auth:sanctum', Ugyintezo::class])
->group(function () {
    //jelentkezők alapadatainak kilistázása
    
    //jelentkezes állapotának módosítása

    //kik jelentkeztek arra a szakra
    Route::get("/szakra-jelentkezett/{szak}", [SzakController::class, 'getJelentkezokSzakra']);

    //hanyan jelentkeztek arra a szakra
    Route::get("/jelentkezok-szama/{szak}", [SzakController::class, 'jelentkezokSzamaSzakra']);

    //csak nappali tagozatra jelentkeztek
    Route::get("/nappali-jelentkezok", [JelentkezoController::class, 'nappaliJelentkezok']);

    //csak esti tagozatra jelentkeztek
    Route::get("/esti-jelentkezok", [JelentkezoController::class, 'estiJelentkezok']);

    //csak bizonyos tagozatra jelentkeztek
    Route::get("/tagozat-jelentkezok/{szam}", [JelentkezoController::class, 'csakEgyTagozatraJelentkezett']);

    //hány jelentkezőnek van elfogadva a státusza
    Route::get("/statusz-elfogadva", [JelentkezesController::class, 'elfogadottakSzakonkent']);

    //Nyilatkozat feltöltése
    Route::post('/nyilatkozat-feltoltes', [DokumentumokController::class, 'nyilatkozatFeltolt']);

    //Állapot változás
    Route::patch('/allapot-valtozas', [JelentkezesController::class, 'allapotValtozas']);

    /* ----------------------- Statisztika -----------------------*/

    //hanyan jelentkeztek szakokra bontva
    Route::get("/jelentkezok-szama-statisztika", [SzakController::class, 'jelentkezokSzamaSzakraStat']);

    //hanyan jelentkeztek nappali illetve esti tagozat-ra szakokra bontva
    Route::get("/jelentkezok-tagozatra-bontva", [SzakController::class, 'jelentkezokTagozatonkent']);

    //jelentkezok közül hányat fogadtunk el, össz
    Route::get("/jelentkezok-osszesen-elfogadva", [JelentkezesController::class, 'elfogadottakSzama']);

    //jelentkezok közül hányat fogadtunk el, szakokra bontva
    Route::get("/jelentkezok-szakonkent-elfogadva", [JelentkezesController::class, 'elfogadottakSzamaSzakonkent']);
});



// Master
Route::middleware(['auth:sanctum', Master::class])
    ->group(function () {
    //Ugyintezo felvétele (master)
    Route::post('/uj-ugyintezo', [UgyintezoController::class, 'postUgyintezo']);

    //Ugyintezo törlése
    Route::delete('/delete-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoDelete']);

    //Ugyintezo módosítás
    Route::patch('/modosit-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoPatch']);

    //Ugyintezok lekerese
    Route::get('/ugyintezok', [UgyintezoController::class, 'getUgyintezok']);
});
    

    //archiválás
    //havonta hányan jelentkeztek, regisztráltak (szakonként is)
    //évente hányan jelentkeztek, regisztráltak (szakonként is)
    //ki jelentkezett de nem regisztrált
    //25év feletti