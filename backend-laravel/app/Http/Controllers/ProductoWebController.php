<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductoWebController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        // Usamos la URL base desde el archivo .env
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000') . '/api/productos';
    }

    // Mostrar todos los productos
    public function index()
    {
        try {
            $response = Http::get($this->apiUrl);
            $productos = $response->successful() ? $response->json()['productos'] : [];

            return view('productos.index', compact('productos'));
        } catch (\Exception $e) {
            Log::error("Error al obtener productos: " . $e->getMessage());
            return view('productos.index', ['productos' => []])->withErrors('Error al conectar con la API');
        }
    }

    // Mostrar formulario para crear producto
    public function create()
    {
        return view('productos.create');
    }

    // Guardar nuevo producto en la API
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::post($this->apiUrl, $request->all());

            if ($response->successful()) {
                return redirect('/productos')->with('success', '¡Producto creado correctamente!');
            }

            if ($response->status() === 422) {
                $errors = $response->json()['errors'] ?? [];
                return back()->withErrors($errors)->withInput();
            }

            Log::error('Error al crear producto: ' . $response->body());
            return back()->with('error', 'Error en el servidor API')->withInput();

        } catch (\Exception $e) {
            Log::error('Excepción al conectar con API: ' . $e->getMessage());
            return back()->with('error', 'No se pudo conectar con el servidor')->withInput();
        }
    }

    // Mostrar formulario para editar un producto
    public function editar($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/{$id}");

            if (!$response->successful()) {
                return redirect('/productos')->withErrors('No se pudo obtener el producto.');
            }

            $producto = $response->json()['producto'] ?? $response->json();

            return view('productos.modificar', compact('producto'));
        } catch (\Exception $e) {
            Log::error("Error al obtener producto: " . $e->getMessage());
            return redirect('/productos')->withErrors('Error al conectar con la API');
        }
    }

    // Actualizar producto
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::put("{$this->apiUrl}/{$id}", $request->all());

            if ($response->successful()) {
                return redirect('/productos')->with('success', 'Producto actualizado correctamente.');
            }

            if ($response->status() === 422) {
                $errors = $response->json()['errors'] ?? [];
                return back()->withErrors($errors)->withInput();
            }

            Log::error("Error al actualizar producto: " . $response->body());
            return back()->with('error', 'Error inesperado al actualizar')->withInput();
        } catch (\Exception $e) {
            Log::error("Excepción al actualizar producto: " . $e->getMessage());
            return back()->with('error', 'No se pudo conectar con la API')->withInput();
        }
    }

    // Eliminar producto
    public function eliminar($id)
    {
        try {
            $response = Http::delete("{$this->apiUrl}/{$id}");

            if ($response->successful()) {
                return redirect('/productos')->with('success', 'Producto eliminado correctamente.');
            }

            Log::error("Error al eliminar producto: " . $response->body());
            return back()->with('error', 'Error al eliminar el producto');
        } catch (\Exception $e) {
            Log::error("Excepción al eliminar producto: " . $e->getMessage());
            return back()->with('error', 'No se pudo conectar con la API');
        }
    }
}
