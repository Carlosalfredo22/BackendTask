<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            // Para peticiones AJAX/API devolvemos JSON 401
            abort(response()->json(['error' => 'No autenticado'], 401));
        }

        // Si es petición web normal, sí redireccionamos a login
        return route('login');
    }
}
