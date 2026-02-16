<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValoracionRequest;
use App\Models\CompraVenta;
use App\Models\Valoracion;
use Illuminate\Support\Facades\Auth;

class ValoracionController extends Controller
{
    public function index() {
        $valoraciones = Valoracion::where("id_valorado", "=", auth()->id())
            ->with(['emisor', 'compraventa.producto'])
            ->latest()
            ->paginate(5);

        return response()->json($valoraciones);
    }
    public function store(StoreValoracionRequest $request, CompraVenta $compraventa) {
        // comprobar que no se dejan reseñas a sí mismos
        $idEmisor = Auth::id();
        $idReceptor = $idEmisor == $compraventa->id_comprador ? $compraventa->id_vendedor : $compraventa->id_comprador;

        Valoracion::create([
            'id_valorador' => $idEmisor,
            'id_valorado' => $idReceptor,
            'id_venta' => $compraventa->id,
            'valoracion' => $request->valoracion,
            'comentario' => $request->comentario
        ]);

        // $compraventa::update(['estado' => 'valorado']);
        return response()->json(['status' => 'success']);
    }
}
