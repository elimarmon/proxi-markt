<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraVentaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PuntoEntregaController;
use Illuminate\Support\Facades\Route;

// Rutas de puntos de entrega

Route::get('/puntos_radio/{radio}', [PuntoEntregaController::class, 'puntos_radio']);

// Rutas de productos

Route::get('/productos', [ProductoController::class, 'index']);
Route::post("/productos", [ProductoController::class, "store"]);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::get('/productosporpunto/{id}', [ProductoController::class, 'obtenerProductospunto']);

// Rutas de categorías

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::post("/categorias", [CategoriaController::class, "store"]);

// Rutas de usuarios

Route::post('/register', [AuthController::class, 'createUser'])
    ->name('register');
Route::post('/login', [AuthController::class, 'loginUser'])
    ->name('login');

// Rutas protegidas

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/puntosuser', [PuntoEntregaController::class, 'puntosPorVendedor']);
    Route::post('/insertarpunto', [PuntoEntregaController::class, 'store']);
    Route::get('/datosuser', [UserController::class, 'unUsuario']);
    Route::put('/ubicacionusuario', [UserController::class, 'updateLocation']);
    Route::delete('/deletepunto/{id}', [PuntoEntregaController::class, 'destroy']);
    Route::post('/publicarproducto', [ProductoController::class, 'store']);
    Route::get('/productosuser', [ProductoController::class, 'productosPorUsuario']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
    
    // Rutas de compraventa

    Route::post("/compraventa/{producto}", [CompraVentaController::class, 'store']);
    Route::get('/miscompras', [CompraVentaController::class, 'misCompras']);
    Route::get('/misventas', [CompraVentaController::class, 'misVentas']);
    Route::get('/miscomandas/{id}', [CompraVentaController::class, 'misComandas']);
});
