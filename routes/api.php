<?php

use App\Http\Controllers\JelentkezoController;
use App\Http\Controllers\SzakController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/szakok',[SzakController::class, 'getSzakok']);

Route::post("/ujJelentkezo",[JelentkezoController::class, 'postJelentkezoJelentkezesPortfolio']);