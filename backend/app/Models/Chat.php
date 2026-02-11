<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    // Si tu tabla en la base de datos se llama 'chats', Laravel lo detecta solo,
    // pero es buena práctica ponerlo si quieres estar seguro.
    protected $table = 'chats';

    protected $fillable = ['id_comprador', 'id_vendedor', 'id_producto'];

    /**
     * Relación con los mensajes del chat.
     * Al ser 'hasMany', un chat tiene muchos mensajes.
     */
    public function mensajes()
    {
        // Aquí usamos 'Mensajes::class' porque tu archivo es Mensajes.php
        return $this->hasMany(Mensajes::class, 'id_chat');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'id_comprador');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }
}