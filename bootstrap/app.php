<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*');
        });

        $exceptions->renderable(function (Symfony\Component\Routing\Exception\RouteNotFoundException $e, $request) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated, Please provide a valid token',
            ], 401);
        });
        $exceptions->renderable(function (Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        });
        $exceptions->renderable(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->json([
                'status' => false,
                'message' => 'Page Not Found',
            ], 404);
        });

    })->create();
