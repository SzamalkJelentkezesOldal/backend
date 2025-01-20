<?php

use App\Http\Controllers\DokumentumTipusController;
use App\Http\Controllers\JelentkezesController;
use App\Http\Controllers\JelentkezoController;
use App\Http\Controllers\SzakController;
use App\Http\Controllers\UgyintezoController;
use App\Http\Middleware\Master;
use App\Http\Middleware\Ugyintezo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/szakok', [SzakController::class, 'getSzakok']);
Route::get('/dokumentum-tipusok', [DokumentumTipusController::class, 'getDokumentumTipusok']);


Route::post("/ujJelentkezo", [JelentkezoController::class, 'postJelentkezoJelentkezesPortfolio']);


//Ugyintezo 
Route::middleware(['auth:sanctum', Ugyintezo::class])
    ->group(function () {
        Route::get("/jelentkezok", [JelentkezoController::class, 'index']);
    });

Route::middleware(['auth:sanctum', Master::class])
    ->group(function () {
        //Ugyintezo felvétele
        Route::post('/uj-ugyintezo', [UgyintezoController::class, 'postUgyintezo']);
        //Ugyintezo törlése
        Route::delete('/delete-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoDelete']);
        //Ugyintezo módosítás
        Route::patch('/modosit-ugyintezo/{id}', [UgyintezoController::class, 'ugyintezoPatch']);
        //Hány szakra jelentkezett egy diák

    });
    Route::get('/szakok-szama/{id}', [JelentkezesController::class, 'countJelentkezesSzama']);
    Route::get('/jelentkezo-adatai/{id}', [JelentkezoController::class, 'getJelentkezoAdatok']);

