<?php
// app/Http/Middleware/AdminMiddleware.php
// Garante que apenas usuários administradores possam acessar rotas específicas.

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect('/login'); 
        }

        // 2. Verifica se o usuário é administrador
        if (Auth::user()->is_admin) {
            return $next($request);
        }

        // Caso não seja admin, redireciona para o dashboard com erro
        return redirect('/dashboard')->with('error', 'Acesso não autorizado. Você precisa de permissões de administrador.');
    }
}