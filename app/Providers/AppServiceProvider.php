<?php

namespace App\Providers;

use App\Models\Mensaje;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        
        // Compartir datos globales con todas las vistas Inertia
        Inertia::share([
            'mensajesNoLeidos' => function () {
                if (Auth::check()) {
                    $user = Auth::user();
                    
                    // Obtener todos los chats donde el usuario está involucrado
                    $chatIds = [];
                    
                    if ($user->rol === 'empresa' && $user->empresa) {
                        // Si es empresa, buscar chats de sus solicitudes asignadas
                        $chatIds = \App\Models\Chat::where('empresa_id', $user->empresa->id)
                                    ->pluck('id')
                                    ->toArray();
                    } else if ($user->rol === 'admin') {
                        // Si es admin, puede ver todos los chats
                        $chatIds = \App\Models\Chat::pluck('id')->toArray();
                    } else {
                        // Si es cliente, buscar chats de sus solicitudes
                        $chatIds = \App\Models\Chat::where('cliente_id', $user->id)
                                    ->pluck('id')
                                    ->toArray();
                    }
                    
                    // Contar mensajes no leídos que no son del usuario actual
                    return Mensaje::whereIn('chat_id', $chatIds)
                            ->where('remitente_id', '!=', $user->id)
                            ->where('leido', false)
                            ->count();
                }
                
                return 0;
            },
        ]);
    }
}
