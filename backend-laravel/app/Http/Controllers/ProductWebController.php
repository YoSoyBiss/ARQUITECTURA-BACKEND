<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductWebController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000') . '/api/products';
    }

    // Display all products
    public function index()
    {
        try {
            $response = Http::get($this->apiUrl);
            $products = $response->successful() ? $response->json()['products'] : [];

            return view('products.index', compact('products'));
        } catch (\Exception $e) {
            Log::error("Failed to fetch products: " . $e->getMessage());
            return view('products.index', ['products' => []])->withErrors('Failed to connect to the API');
        }
    }

    // Show form to create a new product
    public function create()
    {
        return view('products.create');
    }

    // Store new product via API
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::post($this->apiUrl, $request->all());

            if ($response->successful()) {
                return redirect('/products')->with('success', 'Product created successfully!');
            }

            if ($response->status() === 422) {
                $errors = $response->json()['errors'] ?? [];
                return back()->withErrors($errors)->withInput();
            }

            Log::error('Error creating product: ' . $response->body());
            return back()->with('error', 'API server error')->withInput();

        } catch (\Exception $e) {
            Log::error('Exception connecting to API: ' . $e->getMessage());
            return back()->with('error', 'Failed to connect to the API server')->withInput();
        }
    }

    // Show form to edit a product
    public function edit($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/{$id}");

            if (!$response->successful()) {
                return redirect('/products')->withErrors('Could not fetch the product.');
            }

            $product = $response->json()['product'] ?? $response->json();

            return view('products.edit', compact('product'));
        } catch (\Exception $e) {
            Log::error("Error fetching product: " . $e->getMessage());
            return redirect('/products')->withErrors('Failed to connect to the API');
        }
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::put("{$this->apiUrl}/{$id}", $request->all());

            if ($response->successful()) {
                return redirect('/products')->with('success', 'Product updated successfully.');
            }

            if ($response->status() === 422) {
                $errors = $response->json()['errors'] ?? [];
                return back()->withErrors($errors)->withInput();
            }

            Log::error("Error updating product: " . $response->body());
            return back()->with('error', 'Unexpected error while updating')->withInput();
        } catch (\Exception $e) {
            Log::error("Exception while updating product: " . $e->getMessage());
            return back()->with('error', 'Failed to connect to the API')->withInput();
        }
    }

    // Delete a product
    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->apiUrl}/{$id}");

            if ($response->successful()) {
                return redirect('/products')->with('success', 'Product deleted successfully.');
            }

            Log::error("Error deleting product: " . $response->body());
            return back()->with('error', 'Failed to delete the product');
        } catch (\Exception $e) {
            Log::error("Exception while deleting product: " . $e->getMessage());
            return back()->with('error', 'Failed to connect to the API');
        }
    }
}
