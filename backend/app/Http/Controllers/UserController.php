<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function unUsuario(Request $request) {
        $user = $request->user();
        $user->loadAvg('valoracionesRecibidas as puntuacion', 'valoracion');
        return response()->json($user);
    }

    public function updateLocation(Request $request, User $usuario) {
        $usuario->update([
            'direccion' => $request->direccion,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $usuario
        ], 201);
    }
}
