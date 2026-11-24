<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validaciones para registrar y actualizar productos (Form Request).
 */
class ProductoRequest extends FormRequest
{
    // Segunda barrera en el backend: solo el administrador puede guardar productos
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->esAdmin();
    }

    public function rules(): array
    {
        return [
            // Obligatorio y sin repetirse (al actualizar se ignora el propio producto)
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos', 'nombre')->ignore($this->route('producto'))],
            'descripcion' => ['required', 'string', 'max:2000'],
            // Debe existir en el catálogo de categorías
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            // Numérico y mayor que cero
            'precio' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            // Entero igual o mayor que cero
            'existencias' => ['required', 'integer', 'min:0'],
            // Solo las opciones permitidas
            'estado' => ['required', Rule::in(Producto::ESTADOS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'categoria_id.required' => 'Selecciona una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.gt' => 'El precio debe ser mayor que cero.',
            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Las existencias deben ser un número entero.',
            'existencias.min' => 'Las existencias no pueden ser negativas.',
            'estado.required' => 'Selecciona un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
