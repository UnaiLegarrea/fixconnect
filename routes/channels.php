<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    if (!$chat) {
        return false;
    }
    
    // El usuario puede acceder al canal si es el cliente o la empresa asociada al chat
    return (int) $user->id === (int) $chat->cliente_id || 
           ((int) $user->empresa_id === (int) $chat->empresa_id && $user->rol === 'empresa') ||
           $user->rol === 'admin';
});
