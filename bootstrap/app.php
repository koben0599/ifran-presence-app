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
        //


        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'enseignant' => \App\Http\Middleware\EnseignantMiddleware::class,
            'coordinateur' => \App\Http\Middleware\CoordinateurMiddleware::class,
            'etudiant' => \App\Http\Middleware\EtudiantMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
