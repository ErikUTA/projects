<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class CheckRole
{
    /**

     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user()->role_id;
        // $roleNames = Role::whereIn('name', $roles)->get();
        // $roleIds = $roleNames->pluck('id')->toArray();

        if (!in_array($user, $roles)) {
            return response()->json(['message' => 'Ruta restringida para este rol'], 403);
        }
        return $next($request);
    }
}

