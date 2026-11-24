<?php

namespace App\Http\Requests;

use App\Http\Controllers\ProductoController;
use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida los filtros, el ordenamiento y la paginación que llegan por la URL.
 */
class FiltroProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El acceso ya lo controla el middleware role:admin,empleado
        return true;
    }

    public function rules(): array
    {
        return [
            'buscar' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
            'precio_min' => ['nullable', 'numeric', 'min:0'],
            'precio_max' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', Rule::in(Producto::ESTADOS)],
            'orden' => ['nullable', Rule::in(array_keys(ProductoController::ORDENES))],
            'por_pagina' => ['nullable', Rule::in(ProductoController::POR_PAGINA)],
        ];
    }

    public function messages(): array
    {
        return [
            'precio_min.numeric' => 'El precio mínimo debe ser un número.',
            'precio_min.min' => 'El precio mínimo no puede ser negativo.',
            'precio_max.numeric' => 'El precio máximo debe ser un número.',
            'precio_max.min' => 'El precio máximo no puede ser negativo.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'orden.in' => 'El criterio de ordenamiento no es válido.',
            'por_pagina.in' => 'Elige 10, 15, 25 o 50 registros por página.',
        ];
    }

    // El precio máximo no puede ser menor que el mínimo
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $minimo = $this->input('precio_min');
            $maximo = $this->input('precio_max');

            if (is_numeric($minimo) && is_numeric($maximo) && (float) $minimo > (float) $maximo) {
                $validator->errors()->add('precio_max', 'El precio máximo no puede ser menor que el mínimo.');
            }
        });
    }
}
