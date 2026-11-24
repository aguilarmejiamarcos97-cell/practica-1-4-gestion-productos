<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroProductoRequest;
use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;

class ProductoController extends Controller
{
    // Opciones de "registros por página"
    public const POR_PAGINA = [10, 15, 25, 50];

    // Criterios de ordenamiento: clave => [texto, columna, dirección]
    public const ORDENES = [
        'fecha_desc' => ['Fecha de registro: más recientes', 'created_at', 'desc'],
        'fecha_asc' => ['Fecha de registro: más antiguos', 'created_at', 'asc'],
        'nombre_asc' => ['Nombre: A-Z', 'nombre', 'asc'],
        'nombre_desc' => ['Nombre: Z-A', 'nombre', 'desc'],
        'precio_desc' => ['Precio: mayor a menor', 'precio', 'desc'],
        'precio_asc' => ['Precio: menor a mayor', 'precio', 'asc'],
        'existencias_desc' => ['Existencias: mayor a menor', 'existencias', 'desc'],
        'existencias_asc' => ['Existencias: menor a mayor', 'existencias', 'asc'],
    ];

    /**
     * Catálogo con filtros combinables, ordenamiento y paginación.
     */
    public function index(FiltroProductoRequest $request)
    {
        $filtros = $request->validated();

        $porPagina = (int) ($filtros['por_pagina'] ?? self::POR_PAGINA[0]);
        $orden = $filtros['orden'] ?? 'fecha_desc';
        [, $columna, $direccion] = self::ORDENES[$orden];

        // with('categoria') carga las categorías de una sola vez (evita consultas de más)
        $consulta = Producto::with('categoria')
            // Buscar por nombre (sin importar mayúsculas)
            ->when(filled($filtros['buscar'] ?? null), function ($q) use ($filtros) {
                $texto = mb_strtolower(addcslashes($filtros['buscar'], '%_\\'));
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%'.$texto.'%']);
            })
            // Filtrar por categoría
            ->when(filled($filtros['categoria_id'] ?? null), fn ($q) => $q->where('categoria_id', $filtros['categoria_id']))
            // Rango de precios
            ->when(filled($filtros['precio_min'] ?? null), fn ($q) => $q->where('precio', '>=', $filtros['precio_min']))
            ->when(filled($filtros['precio_max'] ?? null), fn ($q) => $q->where('precio', '<=', $filtros['precio_max']))
            // Filtrar por estado
            ->when(filled($filtros['estado'] ?? null), fn ($q) => $q->where('estado', $filtros['estado']));

        // Ordenamiento (el nombre se ordena sin distinguir mayúsculas)
        if ($columna === 'nombre') {
            $consulta->orderByRaw('LOWER(nombre) '.$direccion);
        } else {
            $consulta->orderBy($columna, $direccion);
        }

        // El id desempata para que la paginación no repita ni se salte registros
        $consulta->orderBy('id');

        // withQueryString() conserva filtros y orden al cambiar de página
        $productos = $consulta->paginate($porPagina)->withQueryString();

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'ordenes' => array_map(fn ($datos) => $datos[0], self::ORDENES),
            'porPagina' => $porPagina,
            'ordenActual' => $orden,
        ]);
    }

    public function create()
    {
        return view('productos.create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function store(ProductoRequest $request)
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')->with('ok', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function update(ProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')->with('ok', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado correctamente.');
    }
}
