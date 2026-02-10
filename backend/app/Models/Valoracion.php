<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    protected $table = "valoraciones";

    protected $fillable = [
        'valoracion',
        'comentario',
        'id_venta',
        'id_resenyador',
        'id_resenyado',
    ];

    public function compraventa() {
        return $this->belongsTo(CompraVenta::class, 'id_venta', 'id');
    }

    public function emisor() {
        return $this->belongsTo(User::class, 'id_resenyador', 'id');
    }

    public function receptor() {
        return $this->belongsTo(User::class, 'id_resenyado', 'id');
    }

}
