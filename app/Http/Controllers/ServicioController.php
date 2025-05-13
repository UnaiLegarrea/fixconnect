<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Solicitud;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    /**
     * Muestra los detalles de un servicio específico.
     */
    public function show(Solicitud $solicitud)
    {
        // Verificar que el usuario sea de tipo empresa
        if (Auth::user()->rol !== 'empresa') {
            abort(403, 'No autorizado para ver este servicio.');
        }
        
        // Verificar que la solicitud pertenezca a la empresa del usuario
        $empresa = Empresa::where('user_id', Auth::id())->first();
        
        if (!$empresa || $solicitud->empresa_id !== $empresa->id) {
            abort(403, 'No autorizado para ver este servicio.');
        }
        
        // Obtener información del cliente
        $cliente = $solicitud->cliente;
        
        return Inertia::render('Servicio/Show', [
            'solicitud' => [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'descripcion' => $solicitud->descripcion,
                'categoria' => $solicitud->categoria,
                'estado' => $solicitud->estado,
                'fecha' => $solicitud->created_at->format('d/m/Y'),
            ],
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
            ]
        ]);
    }
    
    /**
     * Marca un servicio como completado.
     */
    public function completar(Solicitud $solicitud)
    {
        // Verificar que el usuario sea de tipo empresa
        if (Auth::user()->rol !== 'empresa') {
            abort(403, 'No autorizado para realizar esta acción.');
        }
        
        // Verificar que la solicitud pertenezca a la empresa del usuario
        $empresa = Empresa::where('user_id', Auth::id())->first();
        
        if (!$empresa || $solicitud->empresa_id !== $empresa->id) {
            abort(403, 'No autorizado para realizar esta acción.');
        }
        
        // Verificar que el estado sea 'aceptada'
        if ($solicitud->estado !== 'aceptada') {
            return redirect()->back()->with('error', 'Solo se pueden completar servicios en estado aceptado.');
        }
        
        // Actualizar el estado a 'cerrada'
        $solicitud->estado = 'cerrada';
        $solicitud->save();
        
        return redirect()->route('dashboard')->with('success', 'Servicio marcado como completado correctamente.');
    }
}
