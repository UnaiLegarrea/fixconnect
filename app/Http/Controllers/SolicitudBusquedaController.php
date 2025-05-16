<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolicitudBusquedaController extends Controller
{
    /**
     * Muestra el panel de búsqueda de solicitudes
     */    public function index(Request $request)
    {
        // Verificar que el usuario sea una empresa o admin
        $user = Auth::user();
        if ($user->rol !== 'empresa' && $user->rol !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Solo las empresas y administradores pueden acceder a esta sección.');
        }

        // Obtener categoría de la empresa para filtro por defecto (solo si es empresa)
        $empresaCategoria = null;
        if ($user->rol === 'empresa') {
            $empresaCategoria = $user->empresa->categoria;
        }
        
        // Procesar los parámetros de filtrado
        $query = Solicitud::query()->with('cliente');
        
        // Solo mostrar solicitudes abiertas (las que no tienen empresa asignada)
        $query->where('estado', 'abierta');
        
        // Filtro por categoría (si se proporciona una categoría específica)
        $categoria = $request->input('categoria', $empresaCategoria);
        if ($categoria && $categoria !== 'Todas') {
            $query->where('categoria', $categoria);
        }
        
        // Filtro por texto (buscar en título y descripción)
        $busqueda = $request->input('busqueda');
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('titulo', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }
        
        // Ordenar por fecha de creación (más recientes primero)
        $query->orderBy('created_at', 'desc');
        
        // Paginar los resultados
        $solicitudes = $query->paginate(10)->through(function ($solicitud) {
            return [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'descripcion' => substr($solicitud->descripcion, 0, 150) . (strlen($solicitud->descripcion) > 150 ? '...' : ''),
                'categoria' => $solicitud->categoria,
                'fecha' => $solicitud->created_at->format('d/m/Y'),
                'imagen_url' => $solicitud->imagen_path ? asset('storage/' . $solicitud->imagen_path) : null,
                'cliente' => [
                    'id' => $solicitud->cliente->id,
                    'nombre' => $solicitud->cliente->nombre,
                ]
            ];
        });
        
        // Lista de categorías para el filtro
        $categorias = [
            'Todas',
            'Hogar',
            'Tecnología',
            'Fontanería',
            'Electricidad',
            'Automóvil',
            'General'
        ];
        
        return Inertia::render('Solicitud/Busqueda', [
            'solicitudes' => $solicitudes,
            'categorias' => $categorias,
            'filtros' => [
                'categoria' => $categoria,
                'busqueda' => $busqueda
            ]
        ]);
    }
    
    /**
     * Aceptar una solicitud
     */    public function aceptar(Solicitud $solicitud)
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea una empresa o admin
        if ($user->rol !== 'empresa' && $user->rol !== 'admin') {
            return redirect()->back()->with('error', 'Solo las empresas y administradores pueden aceptar solicitudes.');
        }
        
        // Verificar que la solicitud esté abierta
        if ($solicitud->estado !== 'abierta') {
            return redirect()->back()->with('error', 'Esta solicitud ya no está disponible.');
        }
        
        // Asignar la empresa a la solicitud y cambiar el estado
        if ($user->rol === 'admin') {
            // Para administradores, necesitamos verificar si hay una empresa disponible o crear una ficticia
            $empresas = \App\Models\Empresa::all();
            if ($empresas->isEmpty()) {
                // En caso de no haber empresas, creamos una para el admin
                $empresa = \App\Models\Empresa::create([
                    'user_id' => $user->id,
                    'categoria' => 'Administración',
                    'ubicacion' => 'Central',
                    'verificada' => true
                ]);
                $empresa_id = $empresa->id;
            } else {
                // Usar la primera empresa disponible
                $empresa_id = $empresas->first()->id;
            }
        } else {
            // Para usuarios empresa, usar su ID de empresa
            $empresa_id = $user->empresa->id;
        }
        
        $solicitud->empresa_id = $empresa_id;
        $solicitud->estado = 'aceptada';
        $solicitud->save();
        
        // Crear un chat para la comunicación
        $solicitud->chat()->create([
            'cliente_id' => $solicitud->cliente_id,
            'empresa_id' => $empresa_id,
        ]);
        
        return redirect()->route('solicitudes.chat', $solicitud->id)
                        ->with('success', 'Solicitud aceptada correctamente. Ya puedes comunicarte con el cliente.');
    }
    
    /**
     * Ver detalles de una solicitud
     */    public function show(Solicitud $solicitud)
    {
        $user = Auth::user();
        
        // Verificar que el usuario sea una empresa o admin
        if ($user->rol !== 'empresa' && $user->rol !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Solo las empresas y administradores pueden acceder a esta sección.');
        }
        
        // Verificar que la solicitud esté abierta
        if ($solicitud->estado !== 'abierta') {
            return redirect()->route('solicitud.busqueda')->with('error', 'Esta solicitud ya no está disponible.');
        }
        
        return Inertia::render('Solicitud/Detalle', [
            'solicitud' => [
                'id' => $solicitud->id,
                'titulo' => $solicitud->titulo,
                'descripcion' => $solicitud->descripcion,
                'categoria' => $solicitud->categoria,
                'fecha' => $solicitud->created_at->format('d/m/Y'),
                'imagen_url' => $solicitud->imagen_path ? asset('storage/' . $solicitud->imagen_path) : null,
                'cliente' => [
                    'id' => $solicitud->cliente->id,
                    'nombre' => $solicitud->cliente->nombre,
                ]
            ]
        ]);
    }
}
