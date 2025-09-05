<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            return response()->json([
                'success' => true,
                'user' => $user->email
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    public function register(Request $request) {
        \DB::beginTransaction();
        try {
            $user = User::where('email', $request->get('email'))->first();
            if($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya ha sido registrado'
                ], 500);
            }

            $request->validate([
                'name' => 'required|string',
                'last_name' => 'required|string',
                'maternal_surname' => 'nullable|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role_id' => 'required|integer|exists:roles,id',
                'active' => 'required|boolean',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'last_name.required' => 'El apellido paterno es obligatorio.',
                'maternal_surname.string' => 'El apellido materno debe ser texto válido.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe tener un formato válido.',
                'email.unique' => 'El correo electrónico ya está en uso.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'role_id.required' => 'El rol es obligatorio.',
                'role_id.exists' => 'El rol seleccionado no existe.',
                'active.required' => 'El estado activo/inactivo es obligatorio.',
                'active.boolean' => 'El campo activo debe ser verdadero o falso.',
            ]);

            User::create([
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
                'message' => 'Usuario registrado correctamente'
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

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout completado']);
    }
}
