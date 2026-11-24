{{-- Componente: <x-estado :valor="$producto->estado" /> --}}
@props(['valor'])

<span class="badge {{ $valor === 'disponible' ? 'bg-success' : 'bg-secondary' }}">
    {{ ucfirst($valor) }}
</span>
