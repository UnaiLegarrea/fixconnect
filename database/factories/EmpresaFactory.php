<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        $users = User::where('rol', 'empresa')->pluck('id')->toArray();
        
        if (empty($users)) {
            $user = User::factory()->create(['rol' => 'empresa']);
            $userId = $user->id;
        } else {
            $userId = fake()->randomElement($users);
        }
        
        return [
            'user_id' => $userId,
            'ubicacion' => fake()->address(),
            'documento_path' => 'documentos/empresa_' . fake()->uuid() . '.pdf',
            'verificada' => fake()->boolean(70),
        ];
    }
}
