<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
});

// Rutas protegidas para tareas
Route::middleware(['auth:api','role:admin|user'])->group(function () {
    Route::apiResource('tasks', TaskController::class)->except(['show', 'create', 'edit']);

    // Ruta personalizada para completar una tarea
    Route::put('tasks/{id}/complete', [TaskController::class, 'complete']);
});
