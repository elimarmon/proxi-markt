<?php

namespace App\Http\Controllers;
use Exception;
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

        } catch (Exception $err) {
            DB::rollBack();
            return response()->json(['message' => $err->getMessage()], 500);
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

    public function misComandas() {

        $userId = Auth::id();
        $comandas = CompraVenta::where('id_comprador', '=', $userId)->orWhere('id_vendedor', '=', Auth::id())
            ->with(['producto', 'comprador', 'vendedor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'cantidad_encontrada' => $comandas->count(),
            'datos' => $comandas
        ], 200);
    }

    public function completarVenta(CompraVenta $compraventa) {

        $producto = $producto = Producto::find($compraventa->id_producto);

        switch ($compraventa->estado) {
            case 'completado':
                $producto->decrement('stock_reserva', $compraventa->cantidad);
                $producto->decrement('stock_total', $compraventa->cantidad);
                break;
            case 'cancelado':
                $producto->decrement('stock_reserva', $compraventa->cantidad);
                break;
            case 'en curso':
                break;
            case 'pendiente':
                break;
            default:
                throw new Exception('Estado de transacción erróneo o no procesable.');
        }
    }

    public function actualizarEstado(Request $request, Compraventa $compraventa) {
        $request->validate([
            'estado' => 'required|string|in:pendiente,en curso,cancelado,completado'
        ]);
        
        try {
            DB::beginTransaction();
            $compraventa->update(['estado' => $request->estado]);
            $this->completarVenta($compraventa);
            DB::commit();
            return response()->json(['message' => 'Actualización de stock correcta.'], 201) ; 
        } catch (Exception $err) {
            DB::rollback();
            return response()->json(['message' => $err->getMessage()]);
        }
    }
}