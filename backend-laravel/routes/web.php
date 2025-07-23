<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductWebController;

Route::get('/products/create', function () {
    return view('products.create');
});

Route::post('/products', [ProductWebController::class, 'store'])->name('products.store');

Route::get('/', function () {
    return view('welcome');
});

