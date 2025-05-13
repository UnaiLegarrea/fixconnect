<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Solicitud;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    /**
     * Muestra la interfaz de chat para una solicitud.
     */
    public function show(Solicitud $solicitud)
    {
        $user = Auth::user();
        $isEmpresa = $user->rol === 'empresa';
        
        // Verificar permisos
        if ($isEmpresa) {
            $empresa = $user->empresa;
            if (!$empresa || $solicitud->empresa_id !== $empresa->id) {
                abort(403, 'No autorizado para acceder a este chat.');
            }
            $otherUser = $solicitud->cliente;
        } else {
            if ($solicitud->cliente_id !== $user->id) {
                abort(403, 'No autorizado para acceder a este chat.');
            }
            $otherUser = $solicitud->empresa ? $solicitud->empresa->user : null;
        }
        
        // Si no hay empresa asignada y el usuario no es la empresa, no puede haber chat
        if (!$solicitud->empresa_id && !$isEmpresa) {
            return redirect()->back()->with('error', 'No hay una empresa asignada a esta solicitud para chatear.');
        }
        
        // Obtener o crear chat
        $chat = $solicitud->chat;
        if (!$chat) {
            $chat = Chat::create([
                'solicitud_id' => $solicitud->id,
                'cliente_id' => $solicitud->cliente_id,
                'empresa_id' => $solicitud->empresa_id
            ]);
        }
        
        // Marcar mensajes como leídos
        Mensaje::where('chat_id', $chat->id)
            ->where('remitente_id', '!=', $user->id)
            ->where('leido', false)
            ->update(['leido' => true]);
        
        // Obtener mensajes
        $mensajes = Mensaje::where('chat_id', $chat->id)
            ->with('remitente')
            ->orderBy('created_at')
            ->get()
            ->map(function ($mensaje) {
                return [
                    'id' => $mensaje->id,
                    'contenido' => $mensaje->contenido,
                    'fecha' => $mensaje->created_at->format('d/m/Y H:i'),
                    'remitente' => [
                        'id' => $mensaje->remitente->id,
                        'nombre' => $mensaje->remitente->nombre,
                        'soyYo' => Auth::id() === $mensaje->remitente->id
                    ]
                ];
            });
        
        return Inertia::render('Chat/Show', [
            'solicitud' => [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'estado' => $solicitud->estado
            ],
            'otherUser' => [
                'id' => $otherUser->id,
                'nombre' => $otherUser->nombre
            ],
            'mensajes' => $mensajes,
            'chat' => [
                'id' => $chat->id
            ]
        ]);
    }
    
    /**
     * Envía un nuevo mensaje en el chat.
     */
    public function store(Request $request, Chat $chat)
    {
        $request->validate([
            'contenido' => 'required|string'
        ]);
        
        $user = Auth::user();
        
        // Verificar permisos
        if ($user->rol === 'empresa') {
            $empresa = $user->empresa;
            if (!$empresa || $chat->empresa_id !== $empresa->id) {
                abort(403, 'No autorizado para enviar mensajes en este chat.');
            }
        } else {
            if ($chat->cliente_id !== $user->id) {
                abort(403, 'No autorizado para enviar mensajes en este chat.');
            }
        }
        
        // Crear mensaje
        Mensaje::create([
            'chat_id' => $chat->id,
            'remitente_id' => $user->id,
            'contenido' => $request->contenido,
            'leido' => false
        ]);
        
        return redirect()->back();
    }
    
    /**
     * Muestra una lista de todos los mensajes no leídos del usuario.
     */
    public function index()
    {
        $user = Auth::user();
        $isEmpresa = $user->rol === 'empresa';
        
        // Obtener todos los chats del usuario
        if ($isEmpresa) {
            $chats = Chat::where('empresa_id', $user->empresa->id)
                ->with(['solicitud', 'cliente'])
                ->get();
        } else {
            $chats = Chat::where('cliente_id', $user->id)
                ->with(['solicitud', 'empresa.user'])
                ->get();
        }
        
        $mensajesData = [];
        
        foreach ($chats as $chat) {
            // Obtener el último mensaje de cada chat
            $ultimoMensaje = Mensaje::where('chat_id', $chat->id)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($ultimoMensaje) {
                $noLeidos = Mensaje::where('chat_id', $chat->id)
                    ->where('remitente_id', '!=', $user->id)
                    ->where('leido', false)
                    ->count();
                
                $otherUser = $isEmpresa ? $chat->cliente : ($chat->empresa ? $chat->empresa->user : null);
                
                if ($otherUser) {
                    $mensajesData[] = [
                        'id' => $chat->id,
                        'solicitud' => [
                            'id' => $chat->solicitud->id,
                            'titulo' => $chat->solicitud->titulo
                        ],
                        'otherUser' => [
                            'id' => $otherUser->id,
                            'nombre' => $otherUser->nombre
                        ],
                        'ultimoMensaje' => [
                            'fecha' => $ultimoMensaje->created_at->format('d/m/Y H:i'),
                            'contenido' => $ultimoMensaje->contenido,
                            'esPropio' => $ultimoMensaje->remitente_id === $user->id
                        ],
                        'noLeidos' => $noLeidos
                    ];
                }
            }
        }
        
        // Ordenar por mensajes no leídos y fecha (más recientes primero)
        usort($mensajesData, function ($a, $b) {
            if ($a['noLeidos'] !== $b['noLeidos']) {
                return $b['noLeidos'] - $a['noLeidos']; // Primero los que tienen más no leídos
            }
            return strtotime($b['ultimoMensaje']['fecha']) - strtotime($a['ultimoMensaje']['fecha']); // Luego por fecha
        });
        
        return Inertia::render('Mensaje/Index', [
            'chats' => $mensajesData
        ]);
    }
}
