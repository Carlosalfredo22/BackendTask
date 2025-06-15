<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // 🔒 Esto evita el error de "Route [login] not defined"
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'No autenticado'], 401);
    }
}
