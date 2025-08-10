<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthorController extends Controller
{
    /**
     * GET /api/authors
     * Parámetros opcionales:
     * - q: filtro por nombre (LIKE)
     * - limit: cantidad (default 100, máx 1000)
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $limit = (int) $request->query('limit', 100);
        $limit = max(1, min($limit, 1000));

        $query = Author::query()->orderBy('name');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        return response()->json(
            $query->limit($limit)->get(['id','name'])
        );
    }

    // (Opcional) CRUD básico por si quieres administrarlos desde el API

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
        ]);

        $author = Author::create($data);
        return response()->json($author, 201);
    }

    public function show($id)
    {
        $author = Author::findOrFail($id);
        return response()->json($author);
    }

    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);
        $data = $request->validate([
            'name' => "required|string|max:255|unique:authors,name,{$author->id}",
        ]);

        $author->update($data);
        return response()->json($author);
    }

    public function destroy($id)
    {
        Author::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
