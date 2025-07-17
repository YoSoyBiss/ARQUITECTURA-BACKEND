<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;

Route::get('/productos/crear', function () {
    return view('productos.create');
});
Route::post('/productos', [ProductoWebController::class, 'store'])->name('productos.store');

Route::get('/', function () {
    return view('welcome');
});
