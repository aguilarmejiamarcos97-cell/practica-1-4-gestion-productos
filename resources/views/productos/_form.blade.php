{{-- Formulario compartido por "registrar" y "editar". $producto es null al registrar. --}}

<x-campo name="nombre" label="Nombre del producto" :value="$producto->nombre ?? null" required />

<div class="mb-3">
    <label for="descripcion" class="form-label">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="3"
              class="form-control @error('descripcion') is-invalid @enderror" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="categoria_id" class="form-label">Categoría</label>
    <select id="categoria_id" name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
        <option value="">Selecciona una categoría</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}"
                @selected((string) old('categoria_id', $producto->categoria_id ?? '') === (string) $categoria->id)>
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </select>
    @error('categoria_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <x-campo name="precio" label="Precio" type="number" step="0.01" min="0" :value="$producto->precio ?? null" required />
    </div>

    <div class="col-md-6">
        <x-campo name="existencias" label="Existencias" type="number" step="1" min="0" :value="$producto->existencias ?? null" required />
    </div>
</div>

<div class="mb-3">
    <label for="estado" class="form-label">Estado</label>
    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
        <option value="">Selecciona un estado</option>
        <option value="disponible" @selected(old('estado', $producto->estado ?? '') === 'disponible')>Disponible</option>
        <option value="agotado" @selected(old('estado', $producto->estado ?? '') === 'agotado')>Agotado</option>
    </select>
    @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
