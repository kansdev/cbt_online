<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo('/apps_ade/beranda');
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
