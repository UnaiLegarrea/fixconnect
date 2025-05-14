<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{    public function index()
    {        $user = Auth::user();
        
        // Obtener estadísticas y solicitudes según el tipo de usuario
        if($user->rol === 'empresa'){
            // Si es una empresa, obtener sus solicitudes
            $empresa = $user->empresa;
            
            // Solicitudes pendientes (aceptadas pero no cerradas)
            $solicitudesPendientes = Solicitud::where('empresa_id', $empresa->id)
                                        ->where('estado', 'aceptada')
                                        ->count();
            
            // Solicitudes abiertas (para mostrar solicitudes disponibles)
            $solicitudesAbiertas = Solicitud::where('estado', 'abierta')->count();
            
            // Solicitudes completadas (cerradas)
            $solicitudesCompletadas = Solicitud::where('empresa_id', $empresa->id)
                                        ->where('estado', 'cerrada')
                                        ->count();
            
            // Solicitudes recientes (todas las de esta empresa)
            $solicitudesRecientes = Solicitud::with('cliente')
                                    ->where('empresa_id', $empresa->id)
                                    ->orderBy('updated_at', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($solicitud) {
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'categoria' => $solicitud->categoria,
                                            'estado' => $solicitud->estado,
                                            'fecha' => $solicitud->updated_at->format('Y-m-d')
                                        ];
                                    });
                                    
            // Chats activos (solicitudes aceptadas con chat)
            $chatsActivos = Solicitud::with(['cliente', 'chat.mensajes' => function($query) {
                                        $query->orderBy('created_at', 'desc')->limit(1);
                                    }])
                                    ->where('empresa_id', $empresa->id)
                                    ->where('estado', 'aceptada')
                                    ->whereHas('chat')
                                    ->orderBy('updated_at', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($solicitud) use ($user) {
                                        $ultimoMensaje = $solicitud->chat->mensajes->first();
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'nombreCliente' => $solicitud->cliente->nombre,
                                            'fecha' => $solicitud->updated_at->format('Y-m-d'),
                                            'ultimoMensaje' => $ultimoMensaje ? [
                                                'contenido' => \Illuminate\Support\Str::limit($ultimoMensaje->contenido, 50),
                                                'fecha' => $ultimoMensaje->created_at->format('Y-m-d H:i'),
                                                'esPropio' => $ultimoMensaje->remitente_id === $user->id,
                                                'leido' => $ultimoMensaje->leido
                                            ] : null
                                        ];
                                    });
        } else if($user->rol === 'admin'){
            // Si es administrador, mostrar estadísticas globales
            
            // Solicitudes pendientes (aceptadas)
            $solicitudesPendientes = Solicitud::where('estado', 'aceptada')
                                        ->count();
            
            // Solicitudes abiertas
            $solicitudesAbiertas = Solicitud::where('estado', 'abierta')
                                        ->count();
            
            // Solicitudes completadas (cerradas)
            $solicitudesCompletadas = Solicitud::where('estado', 'cerrada')
                                        ->count();
            
            // Solicitudes recientes (todas, globales)
            $solicitudesRecientes = Solicitud::with(['empresa.user', 'cliente'])
                                    ->orderBy('updated_at', 'desc')
                                    ->take(10)
                                    ->get()
                                    ->map(function ($solicitud) {
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'categoria' => $solicitud->categoria,
                                            'estado' => $solicitud->estado,
                                            'fecha' => $solicitud->updated_at->format('Y-m-d')
                                        ];
                                    });
                                    
            // Chats activos (todas las solicitudes con chat)
            $chatsActivos = Solicitud::with(['cliente', 'empresa.user', 'chat.mensajes' => function($query) {
                                        $query->orderBy('created_at', 'desc')->limit(1);
                                    }])
                                    ->whereHas('chat')
                                    ->orderBy('updated_at', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($solicitud) use ($user) {
                                        $ultimoMensaje = $solicitud->chat->mensajes->first();
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'nombreCliente' => $solicitud->cliente->nombre,
                                            'nombreEmpresa' => $solicitud->empresa ? $solicitud->empresa->user->nombre : 'Sin asignar',
                                            'fecha' => $solicitud->updated_at->format('Y-m-d'),
                                            'ultimoMensaje' => $ultimoMensaje ? [
                                                'contenido' => \Illuminate\Support\Str::limit($ultimoMensaje->contenido, 50),
                                                'fecha' => $ultimoMensaje->created_at->format('Y-m-d H:i'),
                                                'esPropio' => $ultimoMensaje->remitente_id === $user->id,
                                                'leido' => $ultimoMensaje->leido
                                            ] : null
                                        ];
                                    });
        } else {
            // Usuario normal
            // Solicitudes abiertas
            $solicitudesAbiertas = Solicitud::where('cliente_id', $user->id)
                                      ->where('estado', 'abierta')
                                      ->count();
            
            // Solicitudes pendientes (aceptadas)
            $solicitudesPendientes = Solicitud::where('cliente_id', $user->id)
                                        ->where('estado', 'aceptada')
                                        ->count();
            
            // Solicitudes completadas (cerradas)
            $solicitudesCompletadas = Solicitud::where('cliente_id', $user->id)
                                        ->where('estado', 'cerrada')
                                        ->count();
            
            // Solicitudes recientes (todas las del usuario)
            $solicitudesRecientes = Solicitud::with('empresa.user')
                                    ->where('cliente_id', $user->id)
                                    ->orderBy('updated_at', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($solicitud) {
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'categoria' => $solicitud->categoria,
                                            'estado' => $solicitud->estado,
                                            'fecha' => $solicitud->updated_at->format('Y-m-d')
                                        ];
                                    });
                                    
            // Chats activos (solicitudes con chat)
            $chatsActivos = Solicitud::with(['empresa.user', 'chat.mensajes' => function($query) {
                                        $query->orderBy('created_at', 'desc')->limit(1);
                                    }])
                                    ->where('cliente_id', $user->id)
                                    ->whereHas('chat')
                                    ->orderBy('updated_at', 'desc')
                                    ->take(5)
                                    ->get()
                                    ->map(function ($solicitud) use ($user) {
                                        $ultimoMensaje = $solicitud->chat->mensajes->first();
                                        return [
                                            'id' => $solicitud->id,
                                            'titulo' => $solicitud->titulo,
                                            'nombreEmpresa' => $solicitud->empresa ? $solicitud->empresa->user->nombre : 'Sin asignar',
                                            'fecha' => $solicitud->updated_at->format('Y-m-d'),
                                            'ultimoMensaje' => $ultimoMensaje ? [
                                                'contenido' => \Illuminate\Support\Str::limit($ultimoMensaje->contenido, 50),
                                                'fecha' => $ultimoMensaje->created_at->format('Y-m-d H:i'),
                                                'esPropio' => $ultimoMensaje->remitente_id === $user->id,
                                                'leido' => $ultimoMensaje->leido
                                            ] : null
                                        ];
                                    });
        }
        
        // Datos para el dashboard
        $dashboardData = [
            'solicitudesAbiertas' => $solicitudesAbiertas,
            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesCompletadas' => $solicitudesCompletadas,
            'solicitudesRecientes' => $solicitudesRecientes,
            'chatsActivos' => $chatsActivos ?? []
        ];
        
        return Inertia::render('Dashboard', ['stats' => $dashboardData]);
    }
}
