<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admins passam sem restrição
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        // Verifica todos os models resolvidos pela rota
        foreach ($request->route()->parameters() as $model) {
            if (is_object($model) && isset($model->client_id)) {
                if ((int) $model->client_id !== (int) $user->client_id) {
                    abort(403, 'Acesso negado.');
                }
            }
        }

        return $next($request);
    }
}