<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
// routes/api.php
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\GenreController;
// routes/api.php
use App\Http\Controllers\Api\PublisherController;
// routes/api.php
use App\Http\Controllers\Api\ProductSupplierController;
Route::prefix('products/{product}')->group(function () {
    Route::get('suppliers', [ProductSupplierController::class, 'index']);
    Route::post('suppliers', [ProductSupplierController::class, 'store']);
    Route::put('suppliers/{supplierMongoId}', [ProductSupplierController::class, 'update']);
    Route::delete('suppliers/{supplierMongoId}', [ProductSupplierController::class, 'destroy']);
});



Route::apiResource('publishers', PublisherController::class)->only(['index','store','show','update','destroy']);


Route::apiResource('authors', AuthorController::class)->only(['index','store','show','update','destroy']);
Route::apiResource('genres',  GenreController::class)->only(['index','store','show','update','destroy']);


Route::apiResource('products', ProductController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
