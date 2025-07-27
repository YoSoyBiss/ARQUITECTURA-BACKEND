<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Product::all(), 200);
        } catch (\Exception $e) {
            Log::error('Error fetching products', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to fetch products'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'stock' => 'required|integer|min:0'
            ]);

            $product = Product::create($validated);
            return response()->json($product, 201);

        } catch (ValidationException $e) {
            Log::error('Validation error on product store', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected error on product store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Unexpected error occurred'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching product with ID {$id}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'stock' => 'required|integer|min:0'
            ]);

            $product = Product::findOrFail($id);
            $product->update($validated);

            return response()->json($product, 200);

        } catch (ValidationException $e) {
            Log::error('Validation error on product update', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Unexpected error on product update', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Unexpected error occurred'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Product::destroy($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error deleting product with ID {$id}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
    }
}
