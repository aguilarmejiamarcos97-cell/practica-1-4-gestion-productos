{{-- Componente: campo de formulario con su etiqueta y su mensaje de error.
     Uso: <x-campo name="nombre" label="Nombre" :value="$producto->nombre ?? null" /> --}}
@props(['name', 'label', 'type' => 'text', 'value' => null])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>

    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
           {{ $attributes->merge(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')]) }}>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
