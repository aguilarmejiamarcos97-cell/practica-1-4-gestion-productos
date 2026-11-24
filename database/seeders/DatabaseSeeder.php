<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrador', 'password' => 'Admin12345', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'empleado@example.com'],
            ['name' => 'Empleado Prueba', 'password' => 'Empleado12345', 'role' => 'empleado']
        );

        // Catálogo de categorías: nombre => productos base de esa categoría
        $catalogo = [
            'Electrónica' => ['Audífonos', 'Bocina Bluetooth', 'Cargador USB', 'Mouse inalámbrico', 'Teclado'],
            'Hogar' => ['Lámpara', 'Licuadora', 'Juego de sábanas', 'Cafetera', 'Organizador'],
            'Ropa' => ['Playera', 'Sudadera', 'Pantalón de mezclilla', 'Gorra', 'Calcetines'],
            'Deportes' => ['Balón de fútbol', 'Pesas', 'Tapete de yoga', 'Cuerda para saltar', 'Botella deportiva'],
            'Juguetes' => ['Rompecabezas', 'Carrito', 'Muñeca', 'Juego de mesa', 'Bloques de construcción'],
            'Papelería' => ['Cuaderno', 'Plumas', 'Mochila', 'Calculadora', 'Marcatextos'],
        ];

        $i = 0;

        foreach ($catalogo as $nombreCategoria => $productosBase) {
            $categoria = Categoria::firstOrCreate(['nombre' => $nombreCategoria]);

            // Cada producto base en dos modelos: 6 categorías x 5 x 2 = 60 productos
            foreach ($productosBase as $base) {
                foreach (['Básico', 'Pro'] as $modelo) {
                    $i++;

                    // Cada 7 productos se queda sin existencias (agotado)
                    $existencias = ($i % 7 === 0) ? 0 : (($i * 13) % 80) + 1;

                    $producto = Producto::firstOrNew(['nombre' => $base.' '.$modelo]);
                    $producto->forceFill([
                        'descripcion' => $base.' modelo '.$modelo.' de la categoría '.$nombreCategoria.'.',
                        'categoria_id' => $categoria->id,
                        'precio' => 29.90 + (($i * 37) % 970),
                        'existencias' => $existencias,
                        'estado' => $existencias === 0 ? 'agotado' : 'disponible',
                        'created_at' => now()->subDays(60 - $i),
                        'updated_at' => now()->subDays(60 - $i),
                    ])->save();
                }
            }
        }
    }
}
