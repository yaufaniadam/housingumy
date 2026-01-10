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
        $middleware->redirectGuestsTo(fn (\Illuminate\Http\Request $request) => match (true) {
            $request->is('unit-kerja*') => route('unit-kerja.login'),
            $request->is('customer*') => route('customer.login'),
            $request->is('booking/create*') => route('customer.login'),
            $request->is('booking/store*') => route('customer.login'),
            default => route('filament.admin.auth.login'),
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
