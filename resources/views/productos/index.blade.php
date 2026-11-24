@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Catálogo de productos</h3>

    {{-- Solo el administrador ve el botón de registrar --}}
    @if (auth()->user()->esAdmin())
        <a href="{{ route('productos.create') }}" class="btn btn-primary">+ Registrar producto</a>
    @endif
</div>

{{-- Errores de los filtros (por ejemplo, precio máximo menor que el mínimo) --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Filtros, ordenamiento y registros por página (todos se pueden combinar) --}}
<form method="GET" action="{{ route('productos.index') }}" class="card card-body shadow-sm mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <label for="buscar" class="form-label mb-1">Buscar por nombre</label>
            <input type="text" id="buscar" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Ej. audífonos">
        </div>

        <div class="col-md-4">
            <label for="categoria_id" class="form-label mb-1">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-select">
                <option value="">Todas</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected((string) request('categoria_id') === (string) $categoria->id)>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="estado" class="form-label mb-1">Estado</label>
            <select id="estado" name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="disponible" @selected(request('estado') === 'disponible')>Disponible</option>
                <option value="agotado" @selected(request('estado') === 'agotado')>Agotado</option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="precio_min" class="form-label mb-1">Precio mínimo</label>
            <input type="number" step="0.01" min="0" id="precio_min" name="precio_min" value="{{ request('precio_min') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label for="precio_max" class="form-label mb-1">Precio máximo</label>
            <input type="number" step="0.01" min="0" id="precio_max" name="precio_max" value="{{ request('precio_max') }}" class="form-control">
        </div>

        <div class="col-md-4">
            <label for="orden" class="form-label mb-1">Ordenar por</label>
            <select id="orden" name="orden" class="form-select">
                @foreach ($ordenes as $clave => $texto)
                    <option value="{{ $clave }}" @selected($ordenActual === $clave)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="por_pagina" class="form-label mb-1">Por página</label>
            <select id="por_pagina" name="por_pagina" class="form-select">
                @foreach (\App\Http\Controllers\ProductoController::POR_PAGINA as $cantidad)
                    <option value="{{ $cantidad }}" @selected($porPagina === $cantidad)>{{ $cantidad }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-dark">Aplicar</button>
            {{-- Limpiar filtros: vuelve a la lista sin parámetros --}}
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

{{-- Total de registros y página actual --}}
<p class="text-muted mb-2">
    <strong>{{ $productos->total() }}</strong> producto(s) encontrado(s)
    @if ($productos->total() > 0)
        · Mostrando del {{ $productos->firstItem() }} al {{ $productos->lastItem() }}
        · Página <strong>{{ $productos->currentPage() }}</strong> de {{ $productos->lastPage() }}
    @endif
</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Existencias</th>
                    <th>Estado</th>
                    <th>Fecha de registro</th>
                    @if (auth()->user()->esAdmin())
                        <th class="text-end">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($producto->descripcion, 50) }}</td>
                        <td>{{ $producto->categoria->nombre }}</td>
                        <td class="text-end">${{ number_format($producto->precio, 2) }}</td>
                        <td class="text-end">{{ $producto->existencias }}</td>
                        <td><x-estado :valor="$producto->estado" /></td>
                        <td>{{ $producto->created_at->format('d/m/Y') }}</td>

                        {{-- Editar y eliminar: SOLO se muestran al administrador --}}
                        @if (auth()->user()->esAdmin())
                            <td class="text-end">
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                <form method="POST" action="{{ route('productos.destroy', $producto) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar {{ $producto->nombre }}? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->esAdmin() ? 9 : 8 }}" class="text-center text-muted py-4">
                            No se encontraron productos con esos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Los enlaces de paginación conservan los filtros gracias a withQueryString() --}}
<div class="mt-3">
    {{ $productos->links('pagination::bootstrap-5') }}
</div>
@endsection
