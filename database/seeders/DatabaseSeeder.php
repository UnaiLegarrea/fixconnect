<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Solicitud;
use App\Models\Chat;
use App\Models\Mensaje;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios con diferentes roles
        User::factory(5)->create(['rol' => 'cliente']);
        User::factory(3)->create(['rol' => 'empresa']);
        
        // Crear un usuario administrador
        User::factory()->create([
            'nombre' => 'Admin',
            'email' => 'admin@example.com',
            'rol' => 'admin',
        ]);
        
        // Crear empresas asociadas a usuarios empresa
        $usuariosEmpresa = User::where('rol', 'empresa')->get();
        foreach ($usuariosEmpresa as $usuario) {
            Empresa::factory()->create(['user_id' => $usuario->id]);
        }
        
        // Crear solicitudes
        Solicitud::factory(10)->create([
            'estado' => 'abierta',
            'empresa_id' => null,
        ]);
        
        // Obtener IDs de empresas para crear solicitudes aceptadas
        $empresas = Empresa::all();
        if ($empresas->count() > 0) {
            // Crear solicitudes aceptadas con empresas válidas
            foreach ($empresas as $index => $empresa) {
                // Asignar un cliente al azar
                $cliente = User::where('rol', 'cliente')->inRandomOrder()->first();
                
                // Crear solicitud aceptada
                Solicitud::factory()->create([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $empresa->id,
                    'estado' => 'aceptada',
                ]);
                
                // Crear solicitud cerrada si hay suficientes empresas
                if ($index < 3) {
                    Solicitud::factory()->create([
                        'cliente_id' => $cliente->id,
                        'empresa_id' => $empresa->id,
                        'estado' => 'cerrada',
                    ]);
                }
            }
        }
        
        // Crear chats para solicitudes aceptadas
        $solicitudesAceptadas = Solicitud::where('estado', 'aceptada')
            ->whereNotNull('empresa_id')
            ->get();
            
        foreach ($solicitudesAceptadas as $solicitud) {
            Chat::factory()->create([
                'solicitud_id' => $solicitud->id,
                'cliente_id' => $solicitud->cliente_id,
                'empresa_id' => $solicitud->empresa_id,
            ]);
        }
        
        // Crear mensajes para cada chat
        $chats = Chat::all();
        foreach ($chats as $chat) {
            $empresa = Empresa::find($chat->empresa_id);
            $empresaUserId = $empresa->user_id;
            
            // Mensajes del cliente
            Mensaje::factory(5)->create([
                'chat_id' => $chat->id,
                'remitente_id' => $chat->cliente_id,
            ]);
            
            // Mensajes de la empresa
            Mensaje::factory(5)->create([
                'chat_id' => $chat->id,
                'remitente_id' => $empresaUserId,
            ]);
        }
    }
}
