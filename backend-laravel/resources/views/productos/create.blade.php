@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Producto</h1>
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" name="titulo" required>
        </div>
        <!-- Añade más campos aquí (autor, editorial, stock) -->
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection 