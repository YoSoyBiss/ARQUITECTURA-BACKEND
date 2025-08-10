<?php

// app/Http/Controllers/Api/PublisherController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    // GET /api/publishers?q=...&limit=...
    public function index(Request $request)
    {
        $q = $request->query('q');
        $limit = (int) $request->query('limit', 100);
        $limit = max(1, min($limit, 1000));

        $query = Publisher::query()->orderBy('name');
        if ($q) $query->where('name','like',"%{$q}%");

        return response()->json($query->limit($limit)->get(['id','name']));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:publishers,name']);
        $pub = Publisher::create($data);
        return response()->json($pub, 201);
    }

    public function show($id) {
        return response()->json(Publisher::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $pub = Publisher::findOrFail($id);
        $data = $request->validate(['name' => "required|string|max:255|unique:publishers,name,{$pub->id}"]);
        $pub->update($data);
        return response()->json($pub);
    }

    public function destroy($id)
    {
        Publisher::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
