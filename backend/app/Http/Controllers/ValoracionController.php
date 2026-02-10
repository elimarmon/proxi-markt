<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValoracionRequest;
use App\Models\CompraVenta;
use App\Models\Valoracion;

class ValoracionController extends Controller
{
    public function index() {
        $valoraciones = Valoracion::where("id_resenyado", "=", auth()->id())
            ->with(['emisor', 'compraventa.producto'])
            ->latest()
            ->paginate(5);

        return response()->json($valoraciones);
    }
    public function store(StoreValoracionRequest $request, CompraVenta $compraventa) {
        // comprobar que no se dejan reseñas a sí mismos
        $idEmisor = auth()->id();
        $idReceptor = $idEmisor == $compraventa->id_comprador ? $compraventa->id_vendedor : $compraventa->id_comprador;

        Valoracion::create([
            'id_resenyador' => $idEmisor,
            'id_resenyado' => $idReceptor,
            'id_venta' => $compraventa->id,
            'valoracion' => $request->valoracion,
            'comentario' => $request->comentario
        ]);

        $compraventa::update(['estado' => 'valorado']);
        return response()->json(['status' => 'success']);
    }
}
