<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    
    protected $table = 'solicitudes';
    
    protected $fillable = [
        'cliente_id', 'empresa_id', 'titulo', 'descripcion', 'estado', 'categoria', 'imagen_path',
    ];
    
    /**
     * Obtener la URL completa de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if (!$this->imagen_path) {
            return null;
        }
        
        return asset('storage/' . $this->imagen_path);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}


