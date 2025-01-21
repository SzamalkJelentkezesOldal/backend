<?php

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
Route::post('/torzsadat-feltolt', [JelentkezoTorzsController::class, 'torzsadatFeltoltes']);
Route::get('/szakok-szama/{id}', [JelentkezesController::class, 'countJelentkezesSzama']);
Route::get('/jelentkezo-adatai/{id}', [JelentkezoController::class, 'getJelentkezoAdatok']);


// Jelentkező
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

// Ügyintéző 
Route::middleware(['auth:sanctum', Ugyintezo::class])
    ->group(function () {
        Route::get("/jelentkezok", [JelentkezoController::class, 'index']);
    });


// Master
Route::middleware(['auth:sanctum', Master::class])
    ->group(function () {
        //Ugyintezo felvétele
        Route::post('/uj-ugyintezo', [UgyintezoController::class, 'postUgyintezo']);
        //Ugyintezo törlése
        Route::delete('/delete-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoDelete']);
        //Ugyintezo módosítás
        Route::patch('/modosit-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoPatch']);
        


    });
    //kik jelentkeztek arra a szakra
    Route::get("/szakra-jelentkezett/{szak}", [SzakController::class, 'getJelentkezokSzakra']);
    //csak nappali tagozatra jelentkeztek
    Route::get("/nappali-jelentkezok", [JelentkezoController::class, 'nappaliJelentkezok']);
    //csak nappali tagozatra jelentkeztek
    Route::get("/esti-jelentkezok", [JelentkezoController::class, 'estiJelentkezok']);
    //hány jelentkezőnek van elfogadva a státusza
    Route::get("/statusz-elfogadva", [JelentkezesController::class, 'elfogadottakSzakonkent']);



    

    //archiválás
    //havonta hányan jelentkeztek, regisztráltak (szakonként is)
    //évente hányan jelentkeztek, regisztráltak (szakonként is)
    //ki jelentkezett de nem regisztrált
    //25év feletti