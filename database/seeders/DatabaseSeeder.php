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
        User::factory(5)->create(['rol' => 'cliente']);
        User::factory(3)->create(['rol' => 'empresa']);
        
        User::factory()->create([
            'nombre' => 'Admin',
            'email' => 'admin@example.com',
            'rol' => 'admin',
        ]);
        
        $usuariosEmpresa = User::where('rol', 'empresa')->get();
        foreach ($usuariosEmpresa as $usuario) {
            Empresa::factory()->create(['user_id' => $usuario->id]);
        }
        
        Solicitud::factory(10)->create([
            'estado' => 'abierta',
            'empresa_id' => null,
        ]);
        
        $empresas = Empresa::all();
        if ($empresas->count() > 0) {
            foreach ($empresas as $index => $empresa) {
                $cliente = User::where('rol', 'cliente')->inRandomOrder()->first();
                
                Solicitud::factory()->create([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $empresa->id,
                    'estado' => 'aceptada',
                ]);
                
                if ($index < 3) {
                    Solicitud::factory()->create([
                        'cliente_id' => $cliente->id,
                        'empresa_id' => $empresa->id,
                        'estado' => 'cerrada',
                    ]);
                }
            }
        }
        
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
        
        $chats = Chat::all();
        foreach ($chats as $chat) {
            $empresa = Empresa::find($chat->empresa_id);
            $empresaUserId = $empresa->user_id;
            
            Mensaje::factory(5)->create([
                'chat_id' => $chat->id,
                'remitente_id' => $chat->cliente_id,
            ]);
            
            Mensaje::factory(5)->create([
                'chat_id' => $chat->id,
                'remitente_id' => $empresaUserId,
            ]);
        }
    }
}
