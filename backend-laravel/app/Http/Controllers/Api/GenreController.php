<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * GET /api/genres
     * Parámetros opcionales:
     * - q: filtro por nombre (LIKE)
     * - limit: cantidad (default 100, máx 1000)
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $limit = (int) $request->query('limit', 100);
        $limit = max(1, min($limit, 1000));

        $query = Genre::query()->orderBy('name');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        return response()->json(
            $query->limit($limit)->get(['id','name'])
        );
    }

    // (Opcional) CRUD básico

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ]);

        $genre = Genre::create($data);
        return response()->json($genre, 201);
    }

    public function show($id)
    {
        $genre = Genre::findOrFail($id);
        return response()->json($genre);
    }

    public function update(Request $request, $id)
    {
        $genre = Genre::findOrFail($id);
        $data = $request->validate([
            'name' => "required|string|max:255|unique:genres,name,{$genre->id}",
        ]);

        $genre->update($data);
        return response()->json($genre);
    }

    public function destroy($id)
    {
        Genre::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
