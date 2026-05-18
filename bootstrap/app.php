<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\SessionTimeout;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/apps_ade', // Jika belum login, arahkan ke halaman login  
            users: '/apps_ade/beranda' // Jika sudah login, arahkan ke halaman beranda
        );

        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class, // Untuk mencegah pengguna kembali ke halaman sebelumnya setelah logout
            'session-timeout' => SessionTimeout::class, // Untuk mengatur waktu sesi admin agar otomatis logout setelah periode tertentu tidak aktif
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e, $request) {
            // if(request()->is('apps_ade/*')) {
            //     if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            //         return redirect()->route('login')->with('failed', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
            //     }
            // }

            if($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
                return redirect()->back()->with('failed', 'Terjadi kesalahan pada server. Silahkan coba lagi. Server mengatakan : ' . $e->getMessage());
            }
        });
    })->create();
