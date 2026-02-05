<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensajes extends Model
{
    protected $table = 'mensajes';
    protected $fillable = ['id_chat', 'id_envio', 'contenido'];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'id_chat');
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'id_envio');
    }
}
