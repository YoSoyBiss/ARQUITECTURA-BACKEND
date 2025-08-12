[<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductWebController;

Route::get('/', function () {
    return view('welcome');
});

