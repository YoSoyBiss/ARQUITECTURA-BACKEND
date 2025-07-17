<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController; // Si está en la subcarpeta Api

Route::get('/productos', [ProductoController::class, 'index']);

Route::post('/productos', [ProductoController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
