<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\CompraVenta;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // 1. Todos los productos para el stock y el listado
        $productos = Producto::where('id_usuario', $userId)->get();

        // 2. Todas las ventas (vendedor es el usuario)
        $ventas = Compraventa::with('producto', 'comprador')
            ->where('id_vendedor', $userId)
            ->get();

        // 3. Todas las compras (comprador es el usuario)
        $compras = Compraventa::with('producto', 'vendedor')
            ->where('id_comprador', $userId)
            ->get();

        return response()->json([
            'productos' => $productos,
            'ventas' => $ventas,
            'compras' => $compras,
        ]);
    }
}