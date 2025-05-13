<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Muestra el chat para una solicitud específica
     */
    public function show(Solicitud $solicitud)
    {        $user = Auth::user();
        
        // Verificar que el usuario tenga permiso para ver este chat
        if ($user->id !== $solicitud->cliente_id && 
            ($user->rol !== 'empresa' && $user->rol !== 'admin') && 
            ($user->rol === 'empresa' && $user->empresa->id !== $solicitud->empresa_id)) {
            abort(403, 'No autorizado para ver este chat.');
        }
        
        // Buscar o crear el chat para esta solicitud
        $chat = Chat::firstOrCreate(
            ['solicitud_id' => $solicitud->id],
            [
                'cliente_id' => $solicitud->cliente_id,
                'empresa_id' => $solicitud->empresa_id
            ]
        );
        
        // Cargar mensajes y marcarlos como leídos
        $mensajes = Mensaje::where('chat_id', $chat->id)
                    ->with('remitente')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function ($mensaje) use ($user) {
                        // Marcar como leído si no es el remitente
                        if ($mensaje->remitente_id !== $user->id && !$mensaje->leido) {
                            $mensaje->leido = true;
                            $mensaje->save();
                        }
                        
                        return [
                            'id' => $mensaje->id,
                            'contenido' => $mensaje->contenido,
                            'fecha' => $mensaje->created_at->format('d/m/Y H:i'),
                            'remitente' => [
                                'id' => $mensaje->remitente->id,
                                'nombre' => $mensaje->remitente->nombre,
                                'esUsuarioActual' => $mensaje->remitente_id === $user->id
                            ],
                            'leido' => $mensaje->leido
                        ];
                    });
        
        // Cargar información de la empresa y el cliente para el chat
        $empresa = null;
        if ($solicitud->empresa_id) {
            $empresa = $solicitud->empresa->user;
            $empresa = [
                'id' => $solicitud->empresa->id,
                'nombre' => $empresa->nombre,
                'ubicacion' => $solicitud->empresa->ubicacion,
                'verificada' => $solicitud->empresa->verificada,
            ];
        }
        
        $cliente = $solicitud->cliente;
        $cliente = [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
        ];
        
        return Inertia::render('Chat/Show', [
            'chat' => [
                'id' => $chat->id,
                'mensajes' => $mensajes
            ],
            'solicitud' => [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'categoria' => $solicitud->categoria,
                'estado' => $solicitud->estado,
            ],
            'empresa' => $empresa,
            'cliente' => $cliente,
            'usuarioActual' => [
                'id' => $user->id,
                'rol' => $user->rol
            ]
        ]);
    }
    
    /**
     * Envía un nuevo mensaje al chat
     */
    public function enviarMensaje(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'contenido' => 'required|string'
        ]);
        
        $user = Auth::user();
          // Verificar que el usuario tenga permiso para enviar mensajes en este chat
        if ($user->id !== $solicitud->cliente_id && 
            ($user->rol !== 'empresa' && $user->rol !== 'admin') && 
            ($user->rol === 'empresa' && $user->empresa->id !== $solicitud->empresa_id)) {
            abort(403, 'No autorizado para enviar mensajes en este chat.');
        }
        
        // Buscar o crear el chat para esta solicitud
        $chat = Chat::firstOrCreate(
            ['solicitud_id' => $solicitud->id],
            [
                'cliente_id' => $solicitud->cliente_id,
                'empresa_id' => $solicitud->empresa_id
            ]
        );
        
        // Crear nuevo mensaje
        $mensaje = new Mensaje();
        $mensaje->chat_id = $chat->id;
        $mensaje->remitente_id = $user->id;
        $mensaje->contenido = $request->contenido;
        $mensaje->leido = false;
        $mensaje->save();
        
        return back();
    }
}
