<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre', 'email', 'password', 'telefono', 'rol',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'cliente_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function chatsComoCliente()
    {
        return $this->hasMany(Chat::class, 'cliente_id');
    }
}
