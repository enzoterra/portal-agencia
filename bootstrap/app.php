<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Confia em todos os proxies reversos (necessário na Hostinger/shared hosting)
        // Garante que Laravel detecte HTTPS corretamente e defina Secure nos cookies de sessão
        $middleware->trustProxies(at: '*');

        $middleware->redirectUsersTo(function (Request $request) {
            $user = auth()->user();
            return $user?->isAdmin() ? '/admin/painel' : '/portal/painel';
        });

        // Middleware Globais
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\ApplyDynamicSettings::class);

        // Aliases para usar nas rotas
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'client.ownership' => \App\Http\Middleware\EnsureClientOwnership::class,
            'force.https' => \App\Http\Middleware\ForceHttps::class,
        ]);

        // ForceHttps aplicado apenas via alias nas rotas necessárias
        // ou diretamente no servidor em produção (Hostinger já força HTTPS via .htaccess)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Redireciona para login se não autenticado
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        // Página de erro 403 personalizada
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            return response()->view('errors.403', [], 403);
        });

        // Garantir que 404 e 500 usem as views customizadas
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            return response()->view('errors.404', [], 404);
        });


    })
    ->create();