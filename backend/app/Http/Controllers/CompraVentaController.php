<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompraVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class CompraVentaController extends Controller
{
<<<<<<< HEAD

    public function store(Request $request)
    {
        
        $request->validate([
            'id_producto'   => 'required|exists:productos,id',
            'id_comprador'  => 'required|exists:usuarios,id',
            'id_vendedor'   => 'required|exists:usuarios,id',
            'id_punto'      => 'required|exists:puntos_entrega,id',
            'cantidad_total'=> 'required|integer|min:1',
        ]);

 
        $producto = Producto::findOrFail($request->id_producto);
        $cantidad = $request->cantidad_total;

        if ($producto->stock_real < $cantidad) {
            return response()->json([
                'message' => 'No hay suficiente stock disponible.',
                'stock_disponible' => $producto->stock_real
            ], 400);
        }

        try {
            DB::beginTransaction();

            $compraventa = CompraVenta::create([
                'id_producto'   => $request->id_producto,
                'id_comprador'  => $request->id_comprador,
                'id_vendedor'   => $request->id_vendedor,
                'id_punto'      => $request->id_punto,
                'cantidad_total'=> $cantidad,
                'estado'        => 'pendiente', // Estado inicial
            ]);


            $producto->increment('stock_reserva', $cantidad);
=======
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
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264

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

<<<<<<< HEAD
    public function completarVenta($id)
    {
=======
    /**
     * Finalizar una venta (Cuando el comprador recoge el producto)
     */
    public function completarVenta($id) {
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264
        $venta = CompraVenta::findOrFail($id);

        if ($venta->estado !== 'pendiente' && $venta->estado !== 'en curso') {
            return response()->json(['message' => 'La venta no se puede completar'], 400);
        }

<<<<<<< HEAD
=======
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
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264
    }
}