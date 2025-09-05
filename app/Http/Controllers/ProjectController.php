<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function assignUsersToProject(Request $request, $projectId)
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

            $project = Project::whereId($projectId)->first();
            if(!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'El proyecto no existe'
                ], 500);
            }

            $project->users()->sync($request->input('users'));

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuarios del proyecto actualizados correctamente',
                'project' => $project->load('users')
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

    public function getProjects(Request $request, $projectId = null)
    {
        $page = $request->get('page', 0);
        $size = $request->get('size', 30);
        $skip = $page * $size;

        if ($projectId) {
            $project = Project::with('users.tasks')->find($projectId);
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el proyecto'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'projects' => [$project]
            ], 200);
        }

        $query = Project::with('users.tasks')->latest();
        $count = $query->count();

        $projects = $query->skip($skip)->take($size)->get();

        return response()->json([
            'success' => true,
            'page' => $page,
            'size' => $size,
            'count' => $count,
            'projects' => $projects
        ], 200);
    }

    public function createProject(Request $request)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'description.required' => 'La descripción es obligatoria.',
            ]);

            $project = Project::where('name', $request->get('name'))->first();
            if($project) {
                return response()->json([
                    'success' => false,
                    'message' => 'El proyecto ya ha sido creado'
                ], 500);
            }

            Project::create([
                'name' => $request->get('name'),
                'description' => $request->get('description')
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Proyecto creado correctamente'
            ], 200);
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

    public function updateProject(Request $request, $projectId)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'description.required' => 'La descripción es obligatoria.',
            ]);

            $project = Project::whereId($projectId)->first();
            if(!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el proyecto'
                ], 500);
            }

            $project->update([
                'name' => $request->get('name'),
                'description' => $request->get('description')
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Proyecto actualizado correctamente'
            ], 200);
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

    public function deleteProject($projectId)
    {
        \DB::beginTransaction();
        try {
            $project = Project::whereId($projectId)->first();
            if(!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el proyecto'
                ], 500);
            }

            $project->delete();

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Proyecto eliminado correctamente'
            ], 200);
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
