<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    // Estados permitidos
    public const ESTADOS = ['disponible', 'agotado'];

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'existencias',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'existencias' => 'integer',
        ];
    }

    // Cada producto pertenece a una categoría
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
