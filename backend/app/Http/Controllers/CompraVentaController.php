<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompraVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompraVentaController extends Controller
{
    public function store(Request $request, Producto $producto) {
        $compraVentaValidada = $request->validate([
            'id_vendedor' => 'required|exists:usuarios,id',
            'id_punto' => 'required|exists:puntos_entrega,id',
            'fecha_prevista' => 'required|date',
            'cantidad' => "required|integer|min:1|lte:{$producto->stock_real}",
        ]);

        try {
            DB::beginTransaction();

            $compraVentaValidada['id_comprador'] = Auth::id();
            $compraVentaValidada['id_producto'] = $producto->id;
            $compraVentaValidada['precio'] = $producto->precio;

            CompraVenta::create($compraVentaValidada);

            $producto->increment('stock_reserva', $request->cantidad);

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
            $producto->decrement('stock_total', $venta->cantidad);
            $producto->decrement('stock_reserva', $venta->cantidad);

            DB::commit();
            return response()->json(['message' => 'Venta finalizada y stock descontado']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al completar la venta'], 500);
        }
    }

    public function misCompras() {
        $compras = CompraVenta::where('id_comprador', Auth::id())
            ->with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($compras);
    }

    public function misVentas() {
        $ventas = CompraVenta::where('id_vendedor', Auth::id())
                        ->with(['producto', 'comprador'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json($ventas);
    }

    public function misComandas($id) {
        $compra = CompraVenta::with(['producto', 'usuario'])->find($id);
        if (!$compra) {
            return response()->json(['message' => 'Solicitud de compra no encontrada'], 404);
        }

        return response()->json($compra, 200);
    }
}