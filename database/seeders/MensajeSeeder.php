<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chat;
use App\Models\Mensaje;

class MensajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos chats si no existen
        if (Chat::count() === 0) {
            $this->command->info('No hay chats disponibles para crear mensajes');
            return;
        }
        
        $chats = Chat::all();
        $usuarios = User::all();
        
        // Mensajes de ejemplo
        $mensajesTexto = [
            'Hola, ¿cómo puedo ayudarte?',
            'Necesito información sobre este servicio',
            'Claro, ¿qué necesitas saber?',
            'Gracias por tu ayuda',
            'De nada, estamos para ayudar',
            '¿Cuándo podrías venir a revisar el problema?',
            'Estaré disponible mañana por la tarde',
            '¿Cuál es el precio aproximado?',
            'Depende del trabajo, pero aproximadamente 50€',
            'Perfecto, te espero entonces'
        ];
        
        // Crear 30 mensajes aleatorios
        for ($i = 0; $i < 30; $i++) {
            $chat = $chats->random();
            $remitente = $i % 2 === 0 ? $chat->cliente : $usuarios->where('id', '!=', $chat->cliente_id)->random();
            
            Mensaje::create([
                'chat_id' => $chat->id,
                'remitente_id' => $remitente->id,
                'contenido' => $mensajesTexto[array_rand($mensajesTexto)],
                'leido' => rand(0, 1) === 1
            ]);
        }
        
        $this->command->info('Se han creado 30 mensajes de prueba');
    }
}
