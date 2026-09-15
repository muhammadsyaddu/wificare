<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memeriksa role pengguna yang sedang login.
 *
 * Menggunakan method User::hasRole() yang sudah didefinisikan di model.
 * Mendukung multiple role: role:admin,technician
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Pastikan relasi role sudah di-load
        if (! $user->relationLoaded('role')) {
            $user->load('role');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
