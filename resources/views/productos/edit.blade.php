@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4">Actualizar producto</h4>

                <form method="POST" action="{{ route('productos.update', $producto) }}">
                    @csrf
                    @method('PUT')

                    @include('productos._form', ['producto' => $producto])

                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
