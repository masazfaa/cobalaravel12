<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth

class IsSuperadmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user login & role-nya superadmin
        if (Auth::check() && Auth::user()->role !== 'superadmin') {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
        }

        return $next($request);
    }
}
