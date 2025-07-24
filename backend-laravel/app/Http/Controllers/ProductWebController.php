<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductWebController;

Route::get('/', function () {
    return view('welcome');
});

// Mostrar listado de productos (llama al método index que consume la API)
Route::get('/products', [ProductWebController::class, 'index'])->name('products.index');

// Mostrar formulario para crear producto
Route::get('/products/create', [ProductWebController::class, 'create'])->name('products.create');

// Guardar nuevo producto (POST)
Route::post('/products', [ProductWebController::class, 'store'])->name('products.store');

// Mostrar formulario para editar producto existente
Route::get('/products/{id}/edit', [ProductWebController::class, 'edit'])->name('products.edit');

// Actualizar producto (PUT)
Route::put('/products/{id}', [ProductWebController::class, 'update'])->name('products.update');

// Eliminar producto (DELETE)
Route::delete('/products/{id}', [ProductWebController::class, 'destroy'])->name('products.destroy');
