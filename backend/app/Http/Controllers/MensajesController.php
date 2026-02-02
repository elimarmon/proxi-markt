<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensaje;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_vendedor' => 'required|exists:usuarios,id',
            'id_producto' => 'required|exists:productos,id',
            'contenido' => 'required|string'
        ]);

        $id_comprador = auth()->id();

        // esta funcio el que fa es que si no existix un chat
        // el crea automaticament
        $chat = Chat::firstOrCreate([
            'id_comprador' => $id_comprador,
            'id_vendedor'  => $request->id_vendedor,
            'id_producto'  => $request->id_producto,
        ]);

        $mensaje = Mensaje::create([
            'id_chat'   => $chat->id,
            'id_envio'  => $id_comprador,
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
