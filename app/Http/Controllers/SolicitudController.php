<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva solicitud.
     */
    public function create()
    {
        $categorias = [
            ['id' => 1, 'nombre' => 'Hogar'],
            ['id' => 2, 'nombre' => 'Tecnología'],
            ['id' => 3, 'nombre' => 'Fontanería'],
            ['id' => 4, 'nombre' => 'Electricidad'],
            ['id' => 5, 'nombre' => 'Automóvil']
        ];
        
        return Inertia::render('Solicitud/Create', [
            'categorias' => $categorias
        ]);
    }

    /**
     * Almacena una nueva solicitud en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagenPath = null;

        // Procesar la imagen si se ha subido
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $imagenPath = $request->file('imagen')->store('solicitudes', 'public');
        }

        Solicitud::create([
            'cliente_id' => Auth::id(),
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'estado' => 'abierta',
            'imagen_path' => $imagenPath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Solicitud creada correctamente');
    }
    
    /**
     * Muestra los detalles de una solicitud específica.
     */
    public function show(Solicitud $solicitud)
    {
        // Verificar que el usuario sea el cliente de la solicitud o admin
        $user = Auth::user();
        if ($user->id !== $solicitud->cliente_id && $user->rol !== 'admin') {
            abort(403, 'No autorizado para ver esta solicitud.');
        }
        
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
        
        return Inertia::render('Solicitud/Show', [
            'solicitud' => [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'descripcion' => $solicitud->descripcion,
                'categoria' => $solicitud->categoria,
                'estado' => $solicitud->estado,
                'fecha' => $solicitud->created_at->format('d/m/Y'),
                'imagen_url' => $solicitud->imagen_path ? asset('storage/' . $solicitud->imagen_path) : null,
            ],
            'empresa' => $empresa
        ]);
    }
    
    /**
     * Cancela una solicitud existente.
     */
    public function cancelar(Solicitud $solicitud)
    {
        // Verificar que el usuario sea el cliente de la solicitud o admin
        $user = Auth::user();
        if ($user->id !== $solicitud->cliente_id && $user->rol !== 'admin') {
            abort(403, 'No autorizado para cancelar esta solicitud.');
        }
        
        // Verificar que el estado sea 'abierta'
        if ($solicitud->estado !== 'abierta') {
            return redirect()->back()->with('error', 'Solo se pueden cancelar solicitudes en estado abierto.');
        }
        
        // Eliminar la imagen si existe
        if ($solicitud->imagen_path) {
            Storage::disk('public')->delete($solicitud->imagen_path);
        }
        
        // Eliminar la solicitud
        $solicitud->delete();
        
        return redirect()->route('dashboard')->with('success', 'Solicitud cancelada correctamente.');
    }
}
