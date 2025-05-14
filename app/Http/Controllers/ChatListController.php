<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatListController extends Controller
{
    /**
     * Muestra la lista de chats del usuario
     */
    public function index()
    {
        $user = Auth::user();
        $chats = [];
        
        // Obtener chats según el rol del usuario
        if ($user->rol === 'empresa' && $user->empresa) {
            // Obtener chats de las solicitudes asignadas a esta empresa
            $chats = Solicitud::with(['cliente', 'chat.mensajes' => function($query) {
                        $query->orderBy('created_at', 'desc')->limit(1);
                    }])
                    ->where('empresa_id', $user->empresa->id)
                    ->whereHas('chat')
                    ->orderBy('updated_at', 'desc')
                    ->get();
        } elseif ($user->rol === 'admin') {
            // Administrador puede ver todos los chats
            $chats = Solicitud::with(['cliente', 'empresa.user', 'chat.mensajes' => function($query) {
                        $query->orderBy('created_at', 'desc')->limit(1);
                    }])
                    ->whereHas('chat')
                    ->orderBy('updated_at', 'desc')
                    ->get();
        } else {
            // Cliente ve sus propios chats
            $chats = Solicitud::with(['empresa.user', 'chat.mensajes' => function($query) {
                        $query->orderBy('created_at', 'desc')->limit(1);
                    }])
                    ->where('cliente_id', $user->id)
                    ->whereHas('chat')
                    ->orderBy('updated_at', 'desc')
                    ->get();
        }
        
        // Formatear datos para la vista
        $chatsList = $chats->map(function($solicitud) use ($user) {
            $otherUser = $user->rol === 'cliente' 
                ? ($solicitud->empresa ? $solicitud->empresa->user : null)
                : $solicitud->cliente;
            
            $ultimoMensaje = $solicitud->chat->mensajes->first();
            
            return [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'estado' => $solicitud->estado,
                'categoria' => $solicitud->categoria,
                'fecha' => $solicitud->created_at->format('d/m/Y'),
                'otherUser' => $otherUser ? [
                    'id' => $otherUser->id,
                    'nombre' => $otherUser->nombre,
                    'rol' => $user->rol === 'cliente' ? 'empresa' : 'cliente'
                ] : null,
                'empresa' => $solicitud->empresa ? [
                    'id' => $solicitud->empresa->id,
                    'nombre' => $solicitud->empresa->user ? $solicitud->empresa->user->nombre : 'N/A', 
                    'verificada' => $solicitud->empresa->verificada
                ] : null,
                'ultimoMensaje' => $ultimoMensaje ? [
                    'contenido' => $ultimoMensaje->contenido,
                    'fecha' => $ultimoMensaje->created_at->diffForHumans(),
                    'leido' => $ultimoMensaje->leido,
                    'esPropio' => $ultimoMensaje->remitente_id === $user->id
                ] : null,
                'mensajesNoLeidos' => $solicitud->chat->mensajes()
                                ->where('remitente_id', '!=', $user->id)
                                ->where('leido', false)
                                ->count()
            ];
        });
        
        return Inertia::render('Chat/List', [
            'chats' => $chatsList
        ]);
    }
}
