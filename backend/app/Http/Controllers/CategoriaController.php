<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function store(Request $request) {
        try {
            $categoria = Categoria::create([
                "nombre_categoria" => $request->nombre_categoria,
            ]);

            return response()->json(["message" => "Categoria creada"], 201);
        } catch (\Exception $err) {
            return response()->json(["error" => $err], 500);
        }
    }
}
