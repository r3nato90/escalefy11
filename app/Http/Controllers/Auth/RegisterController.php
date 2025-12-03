<?php
// app/Http/Controllers/Auth/RegisterController.php
// Gerencia o registro de novos usuários, coletando os dados necessários para LXPay.

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Exibe o formulário de registro.
     */
    public function showRegistrationForm(Request $request)
    {
        // Passa o ID do plano selecionado se estiver na query string
        $planId = $request->query('plan_id');
        return view('auth.register', compact('planId'));
    }

    /**
     * Lida com o processo de registro de novo usuário (POST /register).
     * @param \Illuminate\Http\Request $request
     */
    public function register(Request $request)
    {
        // 1. Validação (Inclui CPF/CNPJ)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'document' => ['required', 'string', 'max:20', 'unique:users,document'], // CPF/CNPJ Único
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Criação do Usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'document' => $request->document, // Salva o documento para LXPay
            'password' => Hash::make($request->password),
            'subscription_status' => 'trial', // Novo usuário começa em trial
            'plan_id' => $request->plan_id ?? null, // Se um plano foi pré-selecionado na URL
        ]);

        // 3. Autenticação e Redirecionamento
        Auth::login($user);

        // Se o usuário se registrou com um plano, vai para a seleção de plano (checkout)
        if ($user->plan_id) {
            return redirect()->route('checkout.start', ['plan' => $user->plan_id]);
        }

        // Se não, vai para a seleção de planos para converter o trial em pago
        return redirect()->route('checkout.plan');
    }
}