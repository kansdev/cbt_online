<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {
            $lastActivity = session('last_activity');
            $timeout = config('session.lifetime') * 20;

            if($lastActivity && (time() - $lastActivity) > $timeout) {
                Auth::logout();
                session()->flush();
                return redirect('/apps_ade')->with('failed', 'Sesi anda telah habis. Silahkan login kembali.');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
