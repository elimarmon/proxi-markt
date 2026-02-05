<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValoracionRequest;
use App\Models\CompraVenta;
use App\Models\Valoracion;

class ValoracionController extends Controller
{
    public function store(StoreValoracionRequest $request, CompraVenta $compraventa) {
        // comprobar que no se dejan reseñas a sí mismos
        $idEmisor = auth()->id();
        $idReceptor = $idEmisor == $compraventa->id_comprador ? $compraventa->id_vendedor : $compraventa->id_comprador;

        Valoracion::create([
            'id_resenyador' => $idEmisor,
            'id_resenyado' => $idReceptor,
            'valoracio' => $request->valoracion,
            'comentario' => $request->comentario
        ]);

    }
}
