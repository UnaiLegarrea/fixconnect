<?php

// Script de diagnóstico para depurar problemas con la aceptación de solicitudes

// Carga el entorno de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use App\Models\Solicitud;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;

echo "=== Script de diagnóstico para solicitudes y chats ===\n";

// Verificar usuarios y sus roles
echo "\n== Usuarios ==\n";
$users = User::all(['id', 'nombre', 'email', 'rol'])->toArray();
echo "Total usuarios: " . count($users) . "\n";
foreach ($users as $user) {
    echo "ID: {$user['id']}, Nombre: {$user['nombre']}, Email: {$user['email']}, Rol: {$user['rol']}\n";
}

// Verificar empresas
echo "\n== Empresas ==\n";
$empresas = Empresa::with('user')->get();
echo "Total empresas: " . $empresas->count() . "\n";
foreach ($empresas as $empresa) {
    echo "ID: {$empresa->id}, Usuario ID: {$empresa->user_id}, Usuario: {$empresa->user->nombre}, Categoría: {$empresa->categoria}\n";
}

// Verificar solicitudes
echo "\n== Solicitudes ==\n";
$solicitudes = Solicitud::with(['cliente', 'empresa'])->get();
echo "Total solicitudes: " . $solicitudes->count() . "\n";
foreach ($solicitudes as $solicitud) {
    $empresa = $solicitud->empresa ? $solicitud->empresa->id : "N/A";
    echo "ID: {$solicitud->id}, Título: {$solicitud->titulo}, Cliente: {$solicitud->cliente->nombre}, Empresa: {$empresa}, Estado: {$solicitud->estado}\n";
}

// Verificar chats
echo "\n== Chats ==\n";
$chats = Chat::with(['solicitud', 'cliente', 'empresa'])->get();
echo "Total chats: " . $chats->count() . "\n";
foreach ($chats as $chat) {
    echo "ID: {$chat->id}, Solicitud: {$chat->solicitud_id}, Cliente: {$chat->cliente_id}, Empresa: {$chat->empresa_id}\n";
}

// Verificar usuarios empresa sin empresas asignadas
echo "\n== Usuarios empresa sin empresas asignadas ==\n";
$usuariosEmpresaSinEmpresa = User::where('rol', 'empresa')
    ->whereDoesntHave('empresa')
    ->get(['id', 'nombre', 'email']);
echo "Total: " . $usuariosEmpresaSinEmpresa->count() . "\n";
foreach ($usuariosEmpresaSinEmpresa as $user) {
    echo "ID: {$user->id}, Nombre: {$user->nombre}, Email: {$user->email}\n";
}

// Verificar rutas
echo "\n== Rutas ==\n";
$routes = Route::getRoutes();
$aceptarRoute = null;
foreach ($routes as $route) {
    if (strpos($route->uri, 'aceptar') !== false) {
        echo "URI: {$route->uri}, Método: " . implode('|', $route->methods) . ", Nombre: {$route->getName()}\n";
        if (strpos($route->getName(), 'aceptar') !== false) {
            $aceptarRoute = $route;
        }
    }
}

if ($aceptarRoute) {
    echo "Ruta para aceptar encontrada: {$aceptarRoute->getName()}\n";
} else {
    echo "¡ADVERTENCIA! No se encontró una ruta con 'aceptar' en su nombre.\n";
}

echo "\n=== Fin del diagnóstico ===\n";
