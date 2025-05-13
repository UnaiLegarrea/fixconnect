<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Solicitud;

class SolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios y empresas
        $usuarios = User::where('rol', 'cliente')->get();
        $empresas = Empresa::with('user')->where('verificada', true)->get();
        
        if ($usuarios->isEmpty() || $empresas->isEmpty()) {
            $this->command->info('No hay usuarios o empresas disponibles para crear solicitudes');
            return;
        }
        
        // Categorías disponibles
        $categorias = ['Hogar', 'Tecnología', 'Fontanería', 'Electricidad', 'Automóvil'];
        
        // Estados disponibles
        $estados = ['abierta', 'aceptada', 'cerrada'];
        
        // Crear 20 solicitudes
        for ($i = 0; $i < 20; $i++) {
            $cliente = $usuarios->random();
            $empresa = $i < 10 ? null : $empresas->random();
            
            Solicitud::create([
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa ? $empresa->id : null,
                'titulo' => 'Solicitud ' . ($i + 1) . ' - ' . $categorias[array_rand($categorias)],
                'descripcion' => 'Descripción detallada de la solicitud número ' . ($i + 1),
                'estado' => $estados[array_rand($estados)],
                'categoria' => $categorias[array_rand($categorias)],
            ]);
        }
        
        $this->command->info('Se han creado 20 solicitudes de prueba');
    }
}
