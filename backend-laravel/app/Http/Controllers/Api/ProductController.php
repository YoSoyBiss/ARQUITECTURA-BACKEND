<?php

// app/Http/Controllers/Api/ProductController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Filtros soportados:
     * - q (string)
     * - author (string) | author_ids[] (array<int>)
     * - genre (string)  | genre_ids[]  (array<int>)
     * - publisher_id (int)
     */
    public function index(Request $request)
    {
        try {
            // Normalización de arrays / tipos
            $authorIds   = $request->query('author_ids', []);
            $genreIds    = $request->query('genre_ids',  []);
            $publisherId = $request->query('publisher_id');

            $authorIds   = is_array($authorIds) ? $authorIds : [$authorIds];
            $genreIds    = is_array($genreIds)  ? $genreIds  : [$genreIds];

            $authorIds   = array_values(array_filter($authorIds, fn($v)=>is_numeric($v) && (int)$v > 0));
            $genreIds    = array_values(array_filter($genreIds,  fn($v)=>is_numeric($v) && (int)$v > 0));
            $publisherId = (is_numeric($publisherId) && (int)$publisherId > 0) ? (int)$publisherId : null;

            // Validación ligera
            $request->validate([
                'q'      => 'sometimes|string|max:255',
                'author' => 'sometimes|string|max:255',
                'genre'  => 'sometimes|string|max:255',
            ]);

            $q          = $request->query('q');
            $authorText = $request->query('author');
            $genreText  = $request->query('genre');

            $products = Product::with([
                'publisher:id,name',
                'authors:id,name',
                'genres:id,name',
                'images',
                'supplierCost:id,product_id,precio_proveedor',
            ])
                ->when($q, fn($query) => $query->where('title','like',"%{$q}%"))
                ->when($authorText, fn($q1)=>$q1->whereHas('authors', fn($a)=>$a->where('name','like',"%{$authorText}%")))
                ->when(!empty($authorIds), fn($q1)=>$q1->whereHas('authors', fn($a)=>$a->whereIn('authors.id', $authorIds)))
                ->when($genreText, fn($q1)=>$q1->whereHas('genres', fn($g)=>$g->where('name','like',"%{$genreText}%")))
                ->when(!empty($genreIds), fn($q1)=>$q1->whereHas('genres', fn($g)=>$g->whereIn('genres.id', $genreIds)))
                ->when($publisherId, fn($q1)=>$q1->where('publisher_id', $publisherId))
                ->orderByDesc('id')
                ->get();

            $result = $products->map(fn($p) => $this->toDto($p));

            return response()->json($result, 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching products', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'query'   => $request->all()
            ]);
            return response()->json(['message'=>'Failed to fetch products'], 500);
        }
    }

    /**
     * POST /api/products
     * Acepta 'preciodeproveedor' (opcional) para crear/actualizar la relación 1–1 product_supplier.
     */
    public function store(Request $request)
    {
        try {
            // Añadir log para depurar los datos de la petición
            Log::info('Petición POST a /api/products', ['request_data' => $request->all()]);

            $validated = $request->validate([
                'title'             => 'required|string|max:255',
                'publisher_id'      => 'required|integer|exists:publishers,id',
                'stock'             => 'required|integer|min:0',
                'price'             => 'required|numeric|min:0',
                'preciodeproveedor' => 'nullable|numeric|min:0',

                'author_ids'        => 'sometimes|array',
                'author_ids.*'      => 'integer|exists:authors,id',
                'genre_ids'         => 'sometimes|array',
                'genre_ids.*'       => 'integer|exists:genres,id',

                'images'            => 'sometimes|array',
                'images.*.url'      => 'required_with:images|string',
                'images.*.alt'      => 'nullable|string',
                'images.*.is_main'  => 'boolean'
            ]);

            $product = Product::create([
                'title'        => $validated['title'],
                'publisher_id' => $validated['publisher_id'],
                'stock'        => $validated['stock'],
                'price'        => $validated['price'],
            ]);

            if (!empty($validated['author_ids'] ?? null)) {
                $product->authors()->sync($validated['author_ids']);
            }
            if (!empty($validated['genre_ids'] ?? null)) {
                $product->genres()->sync($validated['genre_ids']);
            }
            if (!empty($validated['images'] ?? null)) {
                foreach ($validated['images'] as $img) {
                    $product->images()->create([
                        'url'     => $img['url'],
                        'alt'     => $img['alt'] ?? null,
                        'is_main' => $img['is_main'] ?? false,
                    ]);
                }
            }

            // Guardar precio de proveedor (1–1)
            if ($request->filled('preciodeproveedor')) {
                $product->supplierCost()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['precio_proveedor' => $request->input('preciodeproveedor')]
                );
            }

            // Responder en formato DTO
            $product->load(['publisher:id,name', 'authors:id,name', 'genres:id,name', 'images', 'supplierCost']);
            return response()->json($this->toDto($product), 201);

        } catch (ValidationException $e) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Unexpected error on product store', ['message'=>$e->getMessage()]);
            return response()->json(['message'=>'Unexpected error occurred'], 500);
        }
    }

    /**
     * GET /api/products/{id}
     */
    public function show($id)
    {
        try {
            $product = Product::with(['publisher:id,name','authors:id,name','genres:id,name','images','supplierCost'])
                ->findOrFail($id);

            return response()->json($this->toDto($product), 200);
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Product not found'], 404);
        }
    }

    /**
     * PUT/PATCH /api/products/{id}
     * Acepta 'preciodeproveedor' (opcional) para crear/actualizar la relación 1–1.
     */
    public function update(Request $request, $id)
    {
        try {
            // Añadir log para depurar los datos de la petición
            Log::info('Petición PUT/PATCH a /api/products/' . $id, ['request_data' => $request->all()]);

            $validated = $request->validate([
                'title'             => 'sometimes|required|string|max:255',
                'publisher_id'      => 'sometimes|required|integer|exists:publishers,id',
                'stock'             => 'sometimes|required|integer|min:0',
                'price'             => 'sometimes|required|numeric|min:0',
                'preciodeproveedor' => 'nullable|numeric|min:0',

                'author_ids'        => 'sometimes|array',
                'author_ids.*'      => 'integer|exists:authors,id',
                'genre_ids'         => 'sometimes|array',
                'genre_ids.*'       => 'integer|exists:genres,id',

                'images'            => 'sometimes|array',
                'images.*.url'      => 'required_with:images|string',
                'images.*.alt'      => 'nullable|string',
                'images.*.is_main'  => 'boolean'
            ]);

            $product = Product::findOrFail($id);
            $product->update($validated);

            if ($request->has('author_ids')) {
                $product->authors()->sync($validated['author_ids'] ?? []);
            }
            if ($request->has('genre_ids')) {
                $product->genres()->sync($validated['genre_ids'] ?? []);
            }
            if ($request->has('images')) {
                $product->images()->delete();
                foreach ($validated['images'] ?? [] as $img) {
                    $product->images()->create([
                        'url'     => $img['url'],
                        'alt'     => $img['alt'] ?? null,
                        'is_main' => $img['is_main'] ?? false,
                    ]);
                }
            }

            // Guardar/actualizar precio de proveedor
            if ($request->filled('preciodeproveedor')) {
                $product->supplierCost()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['precio_proveedor' => $request->input('preciodeproveedor')]
                );
            }

            $product->load(['publisher:id,name','authors:id,name','genres:id,name','images','supplierCost']);
            return response()->json($this->toDto($product), 200);

        } catch (ValidationException $e) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Unexpected error on product update', ['message'=>$e->getMessage()]);
            return response()->json(['message'=>'Unexpected error occurred'], 500);
        }
    }

    /**
     * DELETE /api/products/{id}
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete(); // product_supplier se elimina por cascade
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Failed to delete product'], 500);
        }
    }

    /**
     * Mapea un Product a DTO plano para el front.
     */
    private function toDto($p): array
    {
        return [
            'id'                => $p->id,
            'title'             => $p->title,
            'publisher_id'      => $p->publisher_id,
            'publisher'         => optional($p->publisher)->name,
            'authors'           => $p->authors->map(fn($a)=>['id'=>$a->id,'name'=>$a->name])->values(),
            'genres'            => $p->genres->map(fn($g)=>['id'=>$g->id,'name'=>$g->name])->values(),
            'images'            => $p->images->map(fn($i)=>[
                                        'id'=>$i->id,
                                        'url'=>$i->url,
                                        'alt'=>$i->alt,
                                        'is_main'=>(bool)$i->is_main,
                                      ])->values(),
            'stock'             => (int) $p->stock,
            'price'             => (float) $p->price,
            // Campo nuevo para el front
            'preciodeproveedor' => optional($p->supplierCost)->precio_proveedor !== null
                                        ? (float) $p->supplierCost->precio_proveedor
                                        : null,
            'created_at'        => optional($p->created_at)->toISOString(),
        ];
    }
}