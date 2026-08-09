<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use App\Notifications\IntentosLoginSospechosos;
use App\Services\AuditoriaService;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol' => 'nullable|in:Administrador,Consultor,Auditor,Auditado,Alta Dirección'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol ?? 'Auditado',
            'activo' => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login-attempts:' . $request->ip() . ':' . strtolower($request->email);

        if (!Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::hit($key, 300);

            AuditoriaService::log('LOGIN_FALLIDO', ['email_intento' => $request->email]);   

            if (RateLimiter::attempts($key) >= 3) {
                $userExistente = User::where('email', $request->email)->first();
                $userExistente?->notify(new IntentosLoginSospechosos($request->ip()));
            }

            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->activo) {
            AuditoriaService::log('LOGIN_BLOQUEADO_INACTIVO', ['email' => $user->email]);
            return response()->json([
                'message' => 'Usuario desactivado en el sistema.'
            ], 403);
        }

        RateLimiter::clear($key); 

        $token = $user->createToken('auth_token')->plainTextToken;

        AuditoriaService::log('LOGIN_EXITOSO', ['email' => $user->email]);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        AuditoriaService::log('LOGOUT', ['email' => $request->user()->email]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada y token revocado exitosamente'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}