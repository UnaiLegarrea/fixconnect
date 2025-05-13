<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Solicitud;
use App\Models\Chat;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener solicitudes que tienen empresa asignada
        $solicitudes = Solicitud::whereNotNull('empresa_id')->get();
        
        if ($solicitudes->isEmpty()) {
            $this->command->info('No hay solicitudes con empresa asignada para crear chats');
            return;
        }
        
        // Crear un chat para cada solicitud con empresa
        foreach ($solicitudes as $solicitud) {
            Chat::create([
                'solicitud_id' => $solicitud->id,
                'cliente_id' => $solicitud->cliente_id,
                'empresa_id' => $solicitud->empresa_id
            ]);
        }
        
        $this->command->info('Se han creado ' . $solicitudes->count() . ' chats de prueba');
    }
}
