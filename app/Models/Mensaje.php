<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'chat_id', 'remitente_id', 'contenido', 'leido',
    ];
    
    protected $casts = [
        'leido' => 'boolean',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }
}

