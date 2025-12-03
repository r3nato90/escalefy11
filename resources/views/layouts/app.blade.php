<?php
// resources/views/layouts/app.blade.php

use App\Models\Setting;

// Carrega as configurações de design dinamicamente (para a cor primária)
// Nota: Em um ambiente Laravel real, você faria isso via View Composers ou Blade Services
$settings = Setting::getSettings();
$primary_color = $settings->primary_color ?? '#00BFFF';
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escalefy - Dashboard</title>
    
    <!-- Tailwind CSS CDN para demonstração -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Estilo base futurista e confortável, adaptado para cores dinâmicas */
        :root {
            --primary-color: {{ $primary_color }};
            --bg-color: #0f172a; /* Slate-900 */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #e2e8f0;
        }
        /* Classes dinâmicas baseadas na cor primária (Primary Color) */
        .text-escalefy { color: var(--primary-color); }
        .bg-escalefy { background-color: var(--primary-color); }
        .border-escalefy { border-color: var(--primary-color); }
        .focus\:ring-escalefy:focus { --tw-ring-color: var(--primary-color); }
        .focus\:border-escalefy:focus { border-color: var(--primary-color); }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #000;
            font-weight: 600;
            box-shadow: 0 0 5px var(--primary-color), 0 0 10px var(--primary-color);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            opacity: 0.9;
            box-shadow: 0 0 10px var(--primary-color), 0 0 20px var(--primary-color);
        }
        
        /* Ajustes de responsividade para o Sidebar */
        .sidebar-hide { transform: translateX(-100%); }
        .sidebar-show { transform: translateX(0); }
    </style>
    
    <!-- Alpine.js (para o toggle do menu mobile) -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v3.x.x/dist/alpine.min.js" defer></script>
    
    @livewireStyles
</head>
<body class="antialiased">
    <div x-data="{ sidebarOpen: false, isAdmin: {{ Auth::user()->is_admin ? 'true' : 'false' }} }" class="min-h-screen flex">
        
        <!-- Sidebar Overlay para Mobile -->
        <div class="lg:hidden fixed inset-0 z-40" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak>
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>
        
        <!-- Sidebar (Responsiva) -->
        <div :class="{'sidebar-show': sidebarOpen, 'sidebar-hide': !sidebarOpen}" 
             class="fixed lg:static w-64 bg-gray-900 border-r border-gray-800 p-6 z-50 transition-transform duration-300 ease-in-out lg:translate-x-0">
            
            <h1 class="text-2xl font-extrabold text-escalefy mb-10">Escalefy</h1>
            
            <!-- Navegação Principal -->
            <nav class="space-y-4">
                <a href="{{ route('user.dashboard') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition duration-150">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Links UTM
                </a>
                
                <!-- Navegação Admin (Apenas se for Admin) -->
                <template x-if="isAdmin">
                    <div class="pt-6 border-t border-gray-700 mt-6">
                        <h2 class="text-xs uppercase text-gray-500 mb-3 font-semibold tracking-wider">Administração</h2>
                        <a href="{{ route('admin.settings') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition duration-150">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Configurações
                        </a>
                        <a href="{{ route('admin.plans') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition duration-150">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v.01M16 11v.01M17 6H3l1 14h14l1-14z"></path></svg>
                            Planos
                        </a>
                    </div>
                </template>
            </nav>
            
            <!-- Botão de Logout -->
            <form method="POST" action="/logout" class="mt-10">
                @csrf
                <button type="submit" class="w-full text-left p-3 rounded-lg text-red-400 hover:bg-gray-800 transition duration-150">
                    Sair ({{ Auth::user()->name }})
                </button>
            </form>
        </div>

        <!-- Conteúdo Principal -->
        <main class="flex-1 overflow-y-auto">
            <!-- Barra de Ação Mobile -->
            <div class="bg-gray-900 p-4 border-b border-gray-800 lg:hidden flex items-center justify-between">
                <!-- Botão Hamburger para Mobile -->
                <button @click="sidebarOpen = true" class="p-2 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-escalefy rounded-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <span class="text-white font-semibold">Dashboard</span>
            </div>
            
            <div class="p-4 md:p-10">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>