<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'telefono' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'es_empresa' => 'boolean',
            'ubicacion' => $request->es_empresa ? 'required|string|max:255' : 'nullable|string',
            'categoria' => $request->es_empresa ? 'required|string|max:255' : 'nullable|string',
        ]);

        $user = User::create([
            'nombre' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol' => $request->es_empresa ? 'empresa' : 'cliente',
        ]);

        // Si es una empresa, crear entrada en la tabla de empresas
        if ($request->es_empresa) {
            $user->empresa()->create([
                'ubicacion' => $request->ubicacion,
                'categoria' => $request->categoria,
                'verificada' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
