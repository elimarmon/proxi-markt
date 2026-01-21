<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompraVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class CompraVentaController extends Controller
{
    public function store(Request $request, Producto $producto) {
        $compraVentaValidada = $request->validate([
            'id_comprador' => 'nullable|exists:usuarios,id',
            'id_vendedor' => 'nullable|exists:usuarios,id',
            'id_punto' => 'nullable|exists:puntos_entrega,id',
            'cantidad_total' => "required|integer|min:1|lte:{$producto->stock_real}",
        ]);

        try {
            DB::beginTransaction();

            $compraVentaValidada['id_producto'] = $producto->id;

            CompraVenta::create($compraVentaValidada);

            $producto->increment('stock_reserva', $request->cantidad_total);

            DB::commit();

            return response()->json([
                'message' => 'Reserva creada correctamente',
                'data' => $compraVentaValidada
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Finalizar una venta (Cuando el comprador recoge el producto)
     */
    public function completarVenta($id) {
        $venta = CompraVenta::findOrFail($id);

        if ($venta->estado !== 'pendiente' && $venta->estado !== 'en curso') {
            return response()->json(['message' => 'La venta no se puede completar'], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Cambiar estado
            $venta->update(['estado' => 'completado']);

            // 2. Descontar del Stock Total definitivamente
            $producto = $venta->producto;
            $producto->decrement('stock_total', $venta->cantidad_total);
            $producto->decrement('stock_reserva', $venta->cantidad_total);

            DB::commit();
            return response()->json(['message' => 'Venta finalizada y stock descontado']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al completar la venta'], 500);
        }
    }
}