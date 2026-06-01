<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasienMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $role = auth()->user()->role;

        if ($role !== 'user') {
            abort(403, 'Halaman ini hanya untuk pasien.');
        }

        return $next($request);
    }
}
