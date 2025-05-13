<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {        $user = Auth::user();
        
        // Obtener estadísticas y solicitudes según el tipo de usuario
        if($user->rol === 'empresa'){
            // Si es una empresa, obtener sus solicitudes
            $empresa = $user->empresa;
            
            // Solicitudes pendientes (aceptadas pero no cerradas)
            $solicitudesPendientes = Solicitud::where('empresa_id', $empresa->id)
                                        ->where('estado', 'aceptada')
                                        ->count();
            
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
        } else {
            // Usuario normal
            // Solicitudes pendientes (abiertas o aceptadas)
            $solicitudesPendientes = Solicitud::where('cliente_id', $user->id)
                                        ->whereIn('estado', ['abierta', 'aceptada'])
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
        }
        
        // Datos para el dashboard
        $dashboardData = [
            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesCompletadas' => $solicitudesCompletadas,
            'solicitudesRecientes' => $solicitudesRecientes
        ];
        
        return Inertia::render('Dashboard', ['stats' => $dashboardData]);
    }
}
