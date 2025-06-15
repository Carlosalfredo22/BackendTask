<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Register - Datos recibidos: ', $request->all());

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignar rol por defecto
            $user->assignRole('user');

            Log::info('Register - Usuario creado y rol asignado: ' . $user->email . ' roles: ' . $user->getRoleNames()->implode(', '));

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'user' => $user,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Register - Error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Error en el registro de usuario',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        Log::info('Login - Intento con email: ' . $request->email);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth('api')->attempt($credentials)) {
                Log::warning('Login - Credenciales inválidas para email: ' . $request->email);
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }

            // Cargar roles y permisos explícitamente para el usuario autenticado
            $user = auth('api')->user();
            $user->load('roles', 'permissions');
            Log::info('Login - Usuario autenticado: ' . $user->email . ' roles: ' . $user->getRoleNames()->implode(', '));

            return $this->respondWithToken($token, $user->getRoleNames());

        } catch (\Throwable $e) {
            Log::error('Login - Error en autenticación: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Error en el proceso de autenticación',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        auth('api')->logout();
        Log::info('Logout - Sesión cerrada');
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        Log::info('Refresh - Token renovado');
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token, $roles = [])
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'roles'        => $roles,
        ]);
    }
}
