<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Empresa;
use App\Models\Solicitud;
use App\Models\User;

Route::get('/', function () {
    $empresas = Empresa::with('user')->get()->map(function ($empresa) {
        return [
            'id' => $empresa->id,
            'name' => $empresa->user->nombre,
            'description' => $empresa->user->email, 
            'categoria' => $empresa->categoria, 
            'ubicacion' => $empresa->ubicacion,
            'verificada' => $empresa->verificada
        ];
    });

    $solicitudes = Solicitud::where('estado', 'abierta')
        ->get()
        ->map(function ($solicitud) {
            return [
                'id' => $solicitud->id,
                'nombre' => $solicitud->titulo,
                'descripcion' => $solicitud->descripcion,
                'direccion' => $solicitud->ubicacion ?? '',
                'imagen' => null,
                'categoria' => $solicitud->categoria,
                'estado' => $solicitud->estado
            ];
        });

    $categorias = [
        ['id' => 1, 'nombre' => 'Todas'],
        ['id' => 2, 'nombre' => 'Hogar'],
        ['id' => 3, 'nombre' => 'Tecnología'],
        ['id' => 4, 'nombre' => 'Fontanería'],
        ['id' => 5, 'nombre' => 'Electricidad'],
        ['id' => 6, 'nombre' => 'Automóvil']
    ];

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'empresas' => $empresas,
        'solicitudes' => $solicitudes,
        'categorias' => $categorias
    ]);
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas para solicitudes
    Route::get('/solicitudes/create', [App\Http\Controllers\SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [App\Http\Controllers\SolicitudController::class, 'store'])->name('solicitudes.store');
    
    // Rutas para búsqueda de solicitudes (solo para empresas)
    Route::get('/solicitudes/busqueda', [App\Http\Controllers\SolicitudBusquedaController::class, 'index'])->name('solicitud.busqueda');
    Route::get('/solicitudes/busqueda/{solicitud}', [App\Http\Controllers\SolicitudBusquedaController::class, 'show'])->name('solicitud.busqueda.show');
    Route::post('/solicitudes/busqueda/{solicitud}/aceptar', [App\Http\Controllers\SolicitudBusquedaController::class, 'aceptar'])->name('solicitud.busqueda.aceptar');
    
    // Rutas para detalles de solicitudes específicas (debe ir después de las rutas con prefijos más específicos)
    Route::get('/solicitudes/{solicitud}', [App\Http\Controllers\SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::delete('/solicitudes/{solicitud}/cancelar', [App\Http\Controllers\SolicitudController::class, 'cancelar'])->name('solicitudes.cancelar');
    
    // Rutas para chat
    Route::get('/chats', [App\Http\Controllers\ChatListController::class, 'index'])->name('chat.list');
    Route::get('/solicitudes/{solicitud}/chat', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/solicitudes/{solicitud}/chat/enviar', [App\Http\Controllers\ChatController::class, 'enviarMensaje'])->name('chat.enviar-mensaje');
    
    // Ruta de compatibilidad para redirigir solicitudes.chat a chat.show
    Route::get('/solicitudes/chat/{solicitud}', function(App\Models\Solicitud $solicitud) {
        return redirect()->route('chat.show', $solicitud);
    })->name('solicitudes.chat');
});

require __DIR__.'/auth.php';
