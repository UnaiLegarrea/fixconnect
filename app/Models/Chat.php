<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'solicitud_id', 'cliente_id', 'empresa_id',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }
}

