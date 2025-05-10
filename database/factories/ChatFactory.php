<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    public function definition(): array
    {
        $solicitudes = Solicitud::where('estado', 'aceptada')->pluck('id')->toArray();
        
        if (empty($solicitudes)) {
            $empresa = Empresa::factory()->create();
            $cliente = User::factory()->create(['rol' => 'cliente']);
            $solicitud = Solicitud::factory()->create([
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
                'estado' => 'aceptada'
            ]);
            
            return [
                'solicitud_id' => $solicitud->id,
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa->id,
            ];
        }
        
        $solicitudId = fake()->randomElement($solicitudes);
        $solicitud = Solicitud::find($solicitudId);
        
        return [
            'solicitud_id' => $solicitud->id,
            'cliente_id' => $solicitud->cliente_id,
            'empresa_id' => $solicitud->empresa_id,
        ];
    }
}
