<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensajeFactory extends Factory
{
    public function definition(): array
    {
        $chats = Chat::pluck('id')->toArray();
        
        if (empty($chats)) {
            $chat = Chat::factory()->create();
            $chatId = $chat->id;
            $posiblesRemitentes = [$chat->cliente_id, User::find($chat->empresa_id)->id];
        } else {
            $chatId = fake()->randomElement($chats);
            $chat = Chat::find($chatId);
            $posiblesRemitentes = [$chat->cliente_id, User::find($chat->empresa_id)->id];
        }

        return [
            'chat_id' => $chatId,
            'remitente_id' => fake()->randomElement($posiblesRemitentes),
            'contenido' => fake()->paragraph(),
        ];
    }
}
