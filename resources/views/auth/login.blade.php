<?php
// resources/views/auth/login.blade.php
// Página de Login com opções de e-mail/senha e Login Social.

use App\Models\Setting;
// Carrega as configurações de design dinamicamente
$settings = Setting::getSettings();
$primary_color = $settings->primary_color ?? '#00BFFF';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Escalefy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: {{ $primary_color }}; }
        body { font-family: 'Urbanist', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .text-escalefy { color: var(--primary-color); }
        .bg-escalefy { background-color: var(--primary-color); }
        .focus\:ring-escalefy:focus { --tw-ring-color: var(--primary-color); }
        .focus\:border-escalefy:focus { border-color: var(--primary-color); }
        .btn-social { transition: transform 0.2s; }
        .btn-social:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-gray-900 p-8 rounded-xl shadow-2xl border border-gray-700">
        <h1 class="text-3xl font-extrabold text-white text-center mb-2">Acesse sua conta Escalefy</h1>
        <p class="text-gray-400 text-center mb-8 text-sm">Otimize suas campanhas Meta Ads com precisão.</p>

        <!-- Botões de Login Social -->
        <div class="space-y-4 mb-6">
            <a href="{{ route('social.redirect', 'google') }}" class="btn-social flex items-center justify-center p-3 rounded-lg bg-red-600 text-white font-semibold">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.4H24v7.7h11.2c-.6 3.1-2.9 5.8-6.6 7.4v4.9h6.4c3.9-3.6 6.1-8.7 6.1-14.9 0-1.7-.1-3.3-.4-4.8z"/><path fill="#FF3D00" d="M24 43.6c-7 0-13-3.6-16.4-9.1l5.4-4.2c3.1 2.2 7.1 3.5 11 3.5 4.3 0 8-1.5 11.2-4.1l6.4 4.9c-4.4 4.1-10.4 6.9-17.6 6.9z"/><path fill="#4CAF50" d="M7.6 24c0-2.5.5-4.8 1.4-7L3.6 12.3C2 15.3 1.2 19.5 1.2 24c0 4.5.8 8.7 2.4 11.7l5.4-4.2c-.9-2.2-1.4-4.5-1.4-7z"/><path fill="#1976D2" d="M24 4.4c4.3 0 8.2 1.5 11.2 4.1l4.9-4.9C36.8 1.5 30.8 0 24 0 16.6 0 9.8 2.7 4.9 7.6l5.4 4.2c3.4-5.5 9.4-9.1 16.7-9.1z"/></svg>
                Entrar com Google
            </a>
            <a href="{{ route('social.redirect', 'github') }}" class="btn-social flex items-center justify-center p-3 rounded-lg bg-gray-700 text-white font-semibold border border-gray-600">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.627 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.011-1.04-.017-2.04-3.338.724-4.042-1.61-4.042-1.61-.545-1.385-1.328-1.756-1.328-1.756-1.082-.742.083-.727.083-.727 1.2.084 1.838 1.234 1.838 1.234 1.07 1.83 2.809 1.3 3.492.992.107-.775.418-1.3.762-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.467-2.383 1.233-3.22-.124-.302-.535-1.523.117-3.176 0 0 1.008-.322 3.3-.96.93.25 1.922.375 2.924.379 1.002 0 1.995-.124 2.925-.379 2.292.638 3.3 1.959 3.3 1.959.652 1.653.24 2.874.118 3.176.766.837 1.232 1.91 1.232 3.22 0 4.61-2.806 5.62-5.474 5.918.43.37.822 1.102.822 2.222 0 1.605-.015 2.899-.015 3.286 0 .323.218.694.825.577C20.562 21.82 24 17.303 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                Entrar com GitHub
            </a>
        </div>
        
        <div class="relative flex justify-center text-xs uppercase mb-6">
            <span class="px-2 text-gray-500 bg-gray-900 z-10">ou entre com seu e-mail</span>
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-700"></div>
            </div>
        </div>

        <!-- Formulário de Login (E-mail/Senha) -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            
            @if(session('error'))
                <div class="bg-red-900/50 p-3 rounded-lg text-red-300 text-sm border border-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-400">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-lg p-2.5 focus:ring-escalefy focus:border-escalefy" autocomplete="email">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Senha -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-400">Senha</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-lg p-2.5 focus:ring-escalefy focus:border-escalefy" autocomplete="current-password">
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-escalefy text-gray-900 font-bold py-3 rounded-lg hover:bg-escalefy/90 transition duration-150">
                Fazer Login
            </button>
            
            <div class="text-center text-sm mt-4">
                <a href="{{ route('register') }}" class="text-escalefy hover:underline">Ainda não tem conta? Registre-se agora.</a>
            </div>
        </form>
    </div>
</body>
</html>