<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        try {
            $user = Auth::user();
            Log::info('TaskController@index - Usuario: ' . $user->email . ', roles: ' . $user->getRoleNames()->implode(', '));

            if ($user->hasRole('admin')) {
                $tasks = Task::with('user')->get();
            } else {
                $tasks = $user->tasks()->get();
            }

            return response()->json($tasks);

        } catch (\Throwable $e) {
            Log::error('TaskController@index - Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al obtener las tareas',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            Log::info('TaskController@store - Usuario: ' . $user->email);

            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $task = Task::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'completed' => false,
            ]);

            Log::info('TaskController@store - Tarea creada: ' . $task->id);

            return response()->json($task, 201);

        } catch (\Throwable $e) {
            Log::error('TaskController@store - Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al crear la tarea',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // PUT /api/tasks/{id}/complete
    public function complete($id)
    {
        try {
            $user = Auth::user();
            Log::info('TaskController@complete - Usuario: ' . $user->email . ', tarea id: ' . $id);

            $task = Task::findOrFail($id);

            // Solo admin o dueño de la tarea pueden marcarla como completa
            if ($user->hasRole('user') && $task->user_id != $user->id) {
                Log::warning('TaskController@complete - Usuario no autorizado para completar tarea id: ' . $id);
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->completed = true;
            $task->save();

            Log::info('TaskController@complete - Tarea completada: ' . $id);

            return response()->json($task);

        } catch (\Throwable $e) {
            Log::error('TaskController@complete - Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al completar la tarea',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            Log::info('TaskController@update - Usuario: ' . $user->email . ', tarea id: ' . $id);

            $task = Task::findOrFail($id);

            if ($user->hasRole('user') && $task->user_id != $user->id) {
                Log::warning('TaskController@update - Usuario no autorizado para editar tarea id: ' . $id);
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $request->validate([
                'title' => 'sometimes|string|max:255',
                'completed' => 'sometimes|boolean',
            ]);

            $task->update($request->only(['title', 'completed']));

            Log::info('TaskController@update - Tarea actualizada: ' . $id);

            return response()->json($task);

        } catch (\Throwable $e) {
            Log::error('TaskController@update - Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al actualizar la tarea',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/tasks/{id}
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            Log::info('TaskController@destroy - Usuario: ' . $user->email . ', tarea id: ' . $id);

            $task = Task::findOrFail($id);

            if ($user->hasRole('user') && $task->user_id != $user->id) {
                Log::warning('TaskController@destroy - Usuario no autorizado para eliminar tarea id: ' . $id);
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $task->delete();

            Log::info('TaskController@destroy - Tarea eliminada: ' . $id);

            return response()->json(null, 204);

        } catch (\Throwable $e) {
            Log::error('TaskController@destroy - Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error al eliminar la tarea',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
