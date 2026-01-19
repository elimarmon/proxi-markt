<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraVentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PuntoEntregaController;
use Illuminate\Support\Facades\Route;

// Rutas de productos

Route::get('/productos', [ProductoController::class, 'index']);
Route::post("/productos", [ProductoController::class, "store"]);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);

// Rutas de categorías

Route::get('/categorias', [CategoriasController::class, 'index']);
Route::post("/categorias", [CategoriaController::class, "store"]);

// Rutas de usuarios

Route::post('/register', [AuthController::class, 'createUser'])
    ->name('register');
Route::post('/login', [AuthController::class, 'loginUser'])
    ->name('login');

// Rutas de compraventa

Route::post("/compraventa/{producto}", [CompraVentaController::class, 'store']);

// Rutas protegidas

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/puntosuser', [PuntoEntregaController::class, 'puntosPorVendedor']);
    Route::post('/insertarpunto', [PuntoEntregaController::class, 'store']);
    Route::get('/datosuser', [UserController::class, 'unUsuario']);
    Route::put('/ubicacionusuario', [UserController::class, 'updateLocation']);
    Route::delete('/deletepunto/{id}', [PuntoEntregaController::class, 'destroy']);
    Route::post('/publicarproducto', [ProductoController::class, 'store']);
    Route::get('/productosuser', [ProductoController::class, 'productosPorUsuario']);
});
