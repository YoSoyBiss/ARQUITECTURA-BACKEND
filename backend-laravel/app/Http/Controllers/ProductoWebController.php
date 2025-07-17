<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductoWebController extends Controller
{
     public function create()
    {
        return view('productos.create');
    }

    // Procesa el formulario y envía datos a la API
    public function store(Request $request)
    {
        // Validación local (opcional, adicional a la de la API)
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            // Enviar datos a tu API (usando HTTP Client de Laravel)
            $response = Http::post('http://tu-app.local/api/productos', [
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'editorial' => $request->editorial,
                'stock' => $request->stock
            ]);

            // Si la API responde con éxito (código 2xx)
            if ($response->successful()) {
                return redirect()
                       ->route('productos.create')
                       ->with('success', '¡Producto creado correctamente!');
            }

            // Si la API devuelve errores de validación (código 422)
            if ($response->status() === 422) {
                $errors = $response->json()['errors'] ?? [];
                return back()->withErrors($errors)->withInput();
            }

            // Otros errores de la API
            Log::error('Error al crear producto: ' . $response->body());
            return back()->with('error', 'Error en el servidor API')->withInput();

        } catch (\Exception $e) {
            // Errores de conexión con la API
            Log::error('Excepción al conectar con API: ' . $e->getMessage());
            return back()->with('error', 'No se pudo conectar con el servidor')->withInput();
        }
    }
}
