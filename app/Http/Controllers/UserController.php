<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        try {
            $page = $request->get('page', 0);
            $size = $request->get('size', 30);
            $skip = $page * $size;

            $query = User::with('tasks');
            $count = $query->count();

            $users = $query->skip($skip)->take($size)->get();

            return response()->json([
                'success' => true,
                'page' => $page,
                'size' => $size,
                'count' => $count,
                'users' => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    } 

    public function getUserById($userId)
    {
        try {
            $user = User::with('tasks')->find($userId);
            if(!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no existe'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $userId) {
        \DB::beginTransaction();
        try {
            $user = User::whereId($userId)->first();
            if(!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no existe'
                ], 500);
            }

            $request->validate([
                'name' => 'required|string',
                'last_name' => 'required|string',
                'maternal_surname' => 'nullable|string',
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'role_id' => 'required|integer|exists:roles,id',
                'active' => 'required|boolean',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'last_name.required' => 'El apellido paterno es obligatorio.',
                'maternal_surname.string' => 'El apellido materno debe ser texto válido.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'role_id.required' => 'El rol es obligatorio.',
                'role_id.exists' => 'El rol seleccionado no existe.',
                'active.required' => 'El estado activo/inactivo es obligatorio.',
                'active.boolean' => 'El campo activo debe ser verdadero o falso.',
            ]);

            $user->update([
                'name' => $request->get('name'),
                'last_name' => $request->get('last_name'),
                'maternal_surname' => $request->get('maternal_surname'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
                'role_id' => $request->get('role_id'),
                'active' => $request->get('active'),
            ]);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
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

    public function deleteUser($userId)
    {
        \DB::beginTransaction();
        try {
            $user = User::whereId($userId)->first();
            if(!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el usuario'
                ], 500);
            }

            $user->delete();

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
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
