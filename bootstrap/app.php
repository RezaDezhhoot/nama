<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../routes/web.php',__DIR__.'/../routes/admin.php'],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.auth.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route('admin.dashboard.index'));
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdminMiddleware::class,
            'is_operator' => \App\Http\Middleware\IsOperatorMiddleware::class,
            'has_item' => \App\Http\Middleware\ItemMiddleware::class
        ]);
    })
    ->withBindings([
//        'path.public' => fn() => realpath(base_path().'/../public_html')
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
