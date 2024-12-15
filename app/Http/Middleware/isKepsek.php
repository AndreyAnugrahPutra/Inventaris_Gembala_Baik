<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isKepsek
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard()->user()->id_role === 3) {
            return $next($request);
        }
        return redirect()->back()->with([
            'notif_status' => 'error',
            'notif_message' => 'anda tidak punya akses',
        ]);
    }
}
