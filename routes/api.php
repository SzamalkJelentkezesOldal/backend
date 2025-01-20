<?php

use App\Http\Controllers\DokumentumTipusController;
use App\Http\Controllers\JelentkezoController;
use App\Http\Controllers\SzakController;
use App\Http\Controllers\UgyintezoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/szakok',[SzakController::class, 'getSzakok']);
Route::get('/dokumentum-tipusok',[DokumentumTipusController::class, 'getDokumentumTipusok']);


Route::post("/ujJelentkezo",[JelentkezoController::class, 'postJelentkezoJelentkezesPortfolio']);


//Ugyintezo 
Route::post('/ugyintezo', [UgyintezoController::class, 'postUgyintezo']);