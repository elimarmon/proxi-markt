<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraVentaController;
use Illuminate\Support\Facades\Route;

// Rutas de productos

Route::get('/productos', [ProductoController::class, 'index']);
Route::post("/productos", [ProductoController::class, "store"]);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);

// Rutas de categorías

Route::post("/categorias", [CategoriaController::class, "store"]);

// Rutas de usuarios

Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

// Rutas de compraventa

Route::post("/compraventa/{producto}", [CompraVentaController::class, 'store']);