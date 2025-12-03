<?php
// app/Http/Controllers/SocialController.php
// Gerencia o fluxo de autenticação e registro usando Google e GitHub via Socialite.

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Redireciona para o provedor de autenticação (Google ou GitHub).
     */
    public function redirectToProvider(string $provider)
    {
        // Certifique-se de que o provedor é suportado
        if (!in_array($provider, ['google', 'github'])) {
            return redirect('/login')->with('error', 'Provedor social não suportado.');
        }
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Lida com o callback do provedor (após a autorização).
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::error("Socialite Callback Error for {$provider}: " . $e->getMessage());
            return redirect('/login')->with('error', 'Falha na autenticação via ' . ucfirst($provider));
        }

        // 1. Busca o usuário pelo social_id ou pelo email
        $existingUser = User::where('social_id', $user->getId())
                            ->where('social_provider', $provider)
                            ->first();

        if (!$existingUser) {
            // Se não encontrou por ID social, tenta buscar pelo email
            $existingUser = User::where('email', $user->getEmail())->first();
        }

        if ($existingUser) {
            // Se o usuário já existe (por email ou social_id), faz login
            Auth::login($existingUser, true);
        } else {
            // Se não existe, cria um novo usuário
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'social_id' => $user->getId(),
                'social_provider' => $provider,
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(16)), // Gera uma senha aleatória, pois Socialite não precisa de senha
                'subscription_status' => 'trial', // Começa em trial
                // Documento e telefone são nulos e serão solicitados no primeiro checkout (update-info)
            ]);
            Auth::login($newUser, true);
        }

        // Redireciona para o checkout para escolher o plano
        return redirect()->route('checkout.plan');
    }
}