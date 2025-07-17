<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;   

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los productos
    $productos = Producto::all();

    // Devolver respuesta JSON con los productos
    return response()->json([
        'productos' => $productos
    ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'autor' => 'required|string|max:255',
        'editorial' => 'required|string|max:255',
        'stock' => 'required|integer|min:0'
    ]);

    $producto = Producto::create($request->all());

    return response()->json([
        'message' => 'Producto agregado correctamente',
        'producto' => $producto
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
