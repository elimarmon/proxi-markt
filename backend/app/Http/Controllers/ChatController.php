<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use Illuminate\Http\Request;

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
        ->orderBy('created_at', 'desc') 
        ->get();
    }

    public function show($id)
    {
        // la magia de laravel, porta tots els mensatges relacionats al chat i 
        // tambe porta informacio del producte
        // posem comprador y vendedor perque la tabla chat te estes dos columnes
        $chat = Chat::with('mensajes.emisor', 'producto', 'comprador', 'vendedor')->findOrFail($id);
        
        // per a que ningun usuario sense loguejar puga vore els mensatges
        if (Auth::id() !== $chat->id_comprador && Auth::id() !== $chat->id_vendedor) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $chat;
    }
}