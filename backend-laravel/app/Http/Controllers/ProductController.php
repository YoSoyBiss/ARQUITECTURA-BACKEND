<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'required|string|max:255',
        'stock' => 'required|integer|min:0'
    ]);

    $product = Product::findOrFail($id);
    $product->update($validated);

    return response()->json($product, 200);
}


    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(null, 204);
    }
}
