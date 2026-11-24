<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/productos');

// ---------- Login ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

// ---------- Rutas con sesión iniciada ----------
Route::middleware('auth')->group(function () {

    Route::redirect('/dashboard', '/productos')->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Administrador Y empleado: consultar, buscar, filtrar y ordenar el catálogo
    Route::get('/productos', [ProductoController::class, 'index'])
        ->middleware('role:admin,empleado')
        ->name('productos.index');

    // SOLO administrador: registrar, actualizar y eliminar.
    // Un empleado que escriba estas URLs recibe 403 por el middleware.
    Route::middleware('role:admin')->group(function () {
        Route::resource('productos', ProductoController::class)
            ->except(['index', 'show']);
    });
});
