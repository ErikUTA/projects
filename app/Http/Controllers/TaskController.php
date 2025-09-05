<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Task;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function getTasks(Request $request)
    {
        try {
            $page = $request->get('page', 0);
            $size = $request->get('size', 30);
            $skip = $page * $size;

            $query = Task::query();
            $count = $query->count();

            $tasks = $query->skip($skip)->take($size)->get();

            return response()->json([
                'success' => true,
                'page' => $page,
                'size' => $size,
                'count' => $count,
                'tasks' => $tasks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTaskById($taskId)
    {
        try {
            $task = Task::whereId($taskId)->first();
            if(!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarea no existe'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'task' => $task,
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createTask(Request $request)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'project_id' => 'required|exists:projects,id',
                'status_id' => 'required|exists:task_statuses,id',
                'priority_id' => 'required|exists:task_priorities,id',
                'due_date' => 'required|date|after_or_equal:today',
            ], [
                'title.required' => 'El título es obligatorio.',
                'description.required' => 'La descripción es obligatoria.',
                'project_id.required' => 'Debes seleccionar un proyecto.',
                'project_id.exists' => 'El proyecto seleccionado no existe.',
                'status_id.required' => 'Debes seleccionar un estatus.',
                'status_id.exists' => 'El estatus seleccionado no existe.',
                'priority_id.required' => 'Debes seleccionar una prioridad.',
                'priority_id.exists' => 'La prioridad seleccionada no existe.',
                'due_date.required' => 'La fecha de entrega es obligatoria.',
                'due_date.date' => 'La fecha de entrega debe ser una fecha válida.',
                'due_date.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
            ]);

            $task = Task::where('title', $request->get('title'))->first();
            if($task) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarea ya ha sido creada'
                ], 500);
            }

            Task::create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'project_id' => $request->get('project_id'),
                'status_id' => $request->get('status_id'),
                'priority_id' => $request->get('priority_id'),
                'due_date' => $request->get('due_date'),
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarea creada correctamente',
            ], 200);
        } catch(\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTask(Request $request, $taskId)
    {
        \DB::beginTransaction();
        try {
            $task = Task::whereId($taskId)->first();
            if(!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarea no existe'
                ], 500);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'project_id' => 'required|exists:projects,id',
                'status_id' => 'required|exists:task_statuses,id',
                'priority_id' => 'required|exists:task_priorities,id',
                'due_date' => 'required|date|after_or_equal:today',
            ], [
                'title.required' => 'El título es obligatorio.',
                'description.required' => 'La descripción es obligatoria.',
                'project_id.required' => 'Debes seleccionar un proyecto.',
                'project_id.exists' => 'El proyecto seleccionado no existe.',
                'status_id.required' => 'Debes seleccionar un estatus.',
                'status_id.exists' => 'El estatus seleccionado no existe.',
                'priority_id.required' => 'Debes seleccionar una prioridad.',
                'priority_id.exists' => 'La prioridad seleccionada no existe.',
                'due_date.required' => 'La fecha de entrega es obligatoria.',
                'due_date.date' => 'La fecha de entrega debe ser una fecha válida.',
                'due_date.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
            ]);

            $task->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'project_id' => $request->get('project_id'),
                'status_id' => $request->get('status_id'),
                'priority_id' => $request->get('priority_id'),
                'due_date' => $request->get('due_date'),
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada correctamente',
            ], 200);
        } catch(\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTask($taskId)
    {
        \DB::beginTransaction();
        try {
            $task = Task::find($taskId);
            if(!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
                ], 500);
            }
            $task->delete();

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada correctamente'
            ], 200);
        } catch(\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTaskStatus(Request $request, $taskId)
    {
        \DB::beginTransaction();
        try {
            $task = Task::whereId($taskId)->first();
            if(!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
                ], 500);
            }
            $validator = $request->validate([
                'status_id' => 'required|exists:task_statuses,id'
            ], [
                'status_id.required' => 'Debes seleccionar un estatus.',
                'status_id.exists' => 'El estatus seleccionado no existe.'
            ]);

            $task->update([
                'status_id' => $request->get('status_id'),
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de la tarea actualizado correctamente',
            ], 200);
        } catch(\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function assignUsersToTask(Request $request, $taskId)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'users' => 'required|array',
                'users.*' => 'exists:users,id',
            ], [
                'users.required' => 'Debes enviar al menos un usuario.',
                'users.array'    => 'El campo usuarios debe ser un arreglo.',
                'users.*.exists' => 'Uno de los usuarios seleccionados no existe.',
            ]);

            $task = Task::whereId($taskId)->first();
            if(!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarea no existe'
                ], 500);
            }

            $task->users()->sync($request->input('users'));

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuarios de la tarea actualizados correctamente',
                'task' => $task->load('users')
            ]);
        } catch(AuthorizationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $exception) {
            \DB::rollBack();
            return response()->json([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine()
            ], 500);
        }
    }
}
