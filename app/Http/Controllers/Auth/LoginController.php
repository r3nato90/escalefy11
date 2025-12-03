<?php
// app/Http/Controllers/Auth/LoginController.php
// Gerencia a autenticação manual (POST /login) e o logout.

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Lida com o processo de login (POST).
     * @param \Illuminate\Http\Request $request
     */
    public function login(Request $request)
    {
        // 1. Validação
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Tentativa de Login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // 3. Redirecionamento Pós-Login
            // Verifica se o usuário é administrador ou redireciona para o dashboard
            if (Auth::user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        // 4. Falha de Login
        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas não correspondem aos nossos registros.'],
        ]);
    }

    /**
     * Lida com o processo de logout.
     * @param \Illuminate\Http\Request $request
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}