<?php

use App\Http\Controllers\JelentkezoController;
use App\Http\Controllers\SzakController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/szakok',[SzakController::class, 'getSzakok']);

Route::post("/ujJelentkezo",[JelentkezoController::class, 'postJelentkezoJelentkezesPortfolio']);