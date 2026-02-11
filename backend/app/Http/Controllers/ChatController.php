<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\Mensajes; 
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $usuarioId = $request->user()->id;

        // esta funcio el que fa es mostrar tots els teus chats
        return Chat::where(function($query) use ($usuarioId) {
            $query->where('id_comprador', $usuarioId)
                ->orWhere('id_vendedor', $usuarioId);
        })
        ->with(['producto', 'comprador', 'vendedor', 'mensajes' => function($q) {
            $q->latest()->limit(1); 
        }])
        // He añadido el conteo aquí dentro de la misma consulta, sin romper la cadena.
        ->withCount(['mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
            $query->where('leido', false)
                  ->where('id_envio', '!=', $usuarioId);
        }])
        // -------------------------------
        ->orderBy('updated_at', 'desc') // He puesto updated_at para que suban si hay mensajes nuevos, si prefieres created_at déjalo como estaba.
        ->get();
    }

    public function show($id)
    {
        // la magia de laravel, porta tots els mensatges relacionats al chat i 
        // tambe porta informacio del producte
        // posem comprador y vendedor perque la tabla chat te estes dos columnes
        $chat = Chat::with('mensajes.emisor', 'producto', 'comprador', 'vendedor')->findOrFail($id);
        
        // per a que ningun usuario sense loguejar puga vore els mensatges
        if (auth()->id() !== $chat->id_comprador && auth()->id() !== $chat->id_vendedor) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $chat;
    }

    public function marcarLeido($id)
    {
        $usuarioId = Auth::id();
        
        Mensajes::where('id_chat', $id)
            ->where('id_envio', '!=', $usuarioId)
            ->where('leido', false)
            ->update(['leido' => true]);

        return response()->json(['success' => true]);
    }
}