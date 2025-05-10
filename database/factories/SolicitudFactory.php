<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    public function definition(): array
    {
        $clientes = User::where('rol', 'cliente')->pluck('id')->toArray();
        
        if (empty($clientes)) {
            $cliente = User::factory()->create(['rol' => 'cliente']);
            $clienteId = $cliente->id;
        } else {
            $clienteId = fake()->randomElement($clientes);
        }
        
        $empresaId = null;
        $estado = fake()->randomElement(['abierta', 'aceptada', 'cerrada']);
        
        if ($estado != 'abierta') {
            $empresas = Empresa::pluck('id')->toArray();
            
            if (empty($empresas)) {
                $empresa = Empresa::factory()->create();
                $empresaId = $empresa->id;
            } else {
                $empresaId = fake()->randomElement($empresas);
            }
        }
        
        $categorias = ['Hogar', 'Tecnología', 'Fontanería', 'Electricidad', 'Automóvil', 'General'];
        
        return [
            'cliente_id' => $clienteId,
            'empresa_id' => $empresaId,
            'titulo' => fake()->sentence(),
            'descripcion' => fake()->paragraph(3),
            'categoria' => fake()->randomElement($categorias),
            'estado' => $estado,
        ];
    }
}
