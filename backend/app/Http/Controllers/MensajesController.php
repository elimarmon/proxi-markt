<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensajes;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MensajesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_vendedor' => 'required|exists:usuarios,id',
            'id_producto' => 'required|exists:productos,id',
            'contenido' => 'required|string'
        ]);

        $authId = $request->user()->id; 
        $receptorId = $request->id_vendedor; 
        $productoId = $request->id_producto;

        // aço es per a la diferenciacio de rols per al chat
        $chat = Chat::where('id_producto', $productoId)
            ->where(function ($query) use ($authId, $receptorId) {
                $query->where(function ($q) use ($authId, $receptorId) {
                    // tu eres el comprador y ell el vendedor
                    $q->where('id_comprador', $authId)->where('id_vendedor', $receptorId);
                })->orWhere(function ($q) use ($authId, $receptorId) {
                    // tu eres el vendedor y ell el comprador
                    $q->where('id_comprador', $receptorId)->where('id_vendedor', $authId);
                });
            })->first();

            // si no existix el chat el creem
        if (!$chat) {
            $chat = Chat::create([
                'id_comprador' => $authId,
                'id_vendedor'  => $receptorId,
                'id_producto'  => $productoId,
            ]);
        }

        // creem el mensaje
        $mensaje = Mensajes::create([
            'id_chat'   => $chat->id,
            'id_envio'  => $authId,
            'contenido' => $request->contenido,
        ]);

        // esta funcio el que fa es que al enviar un missatge es recarge la pagina sense fer f5 -> broadcast
        // porta tota la informacio del mensatge -> MessageSent
        // fa que el mensatge aparega en tots els chats menos en el del que la enviat -> toOthers
        broadcast(new MessageSent($mensaje->load('emisor')))->toOthers();

        return response()->json([
            'status' => 'success',
            'chat_id' => $chat->id,
            'mensaje' => $mensaje
        ]);
    }
}
