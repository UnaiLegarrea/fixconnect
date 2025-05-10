<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'ubicacion', 'documento_path', 'verificada',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solicitudesAceptadas()
    {
        return $this->hasMany(Solicitud::class, 'empresa_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'empresa_id');
    }
}

