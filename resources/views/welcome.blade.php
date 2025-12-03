<?php
// resources/views/welcome.blade.php

use App\Models\Setting;
use App\Models\Plan;

// Recupera as configurações e planos do banco de dados
$settings = Setting::getSettings();
$plans = Plan::where('is_active', true)->orderBy('price', 'asc')->get();

$primary_color = $settings->primary_color ?? '#00BFFF';
$secondary_color = $settings->secondary_color ?? '#0a0a0a';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $settings->hero_title ?? 'Escalefy - Otimização Meta Ads' }}</title>
    <meta name="description" content="{{ $settings->hero_subtitle ?? 'Rastreamento, otimização e automação para suas campanhas de Facebook e Instagram.' }}" />
    
    <!-- INCLUSÃO DE FAVICON E LOGOTIPO (LOJOTIPO) -->
    <link rel="icon" type="image/png" href="/images/favicon.png" />
    <!-- Fim da seção de inclusão visual -->

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Define as cores dinâmicas para o design futurista */
        :root {
            --primary-color: {{ $primary_color }};
            --secondary-color: {{ $secondary_color }};
        }
        body {
            font-family: 'Urbanist', sans-serif;
            background-color: var(--secondary-color);
            color: #f8fafc; /* light text */
        }
        .text-primary-dynamic { color: var(--primary-color); }
        .bg-primary-dynamic { background-color: var(--primary-color); }
        .border-primary-dynamic { border-color: var(--primary-color); }
        .btn-cta {
            box-shadow: 0 0 15px var(--primary-color); /* Neon effect */
            transition: all 0.3s ease;
        }
        .btn-cta:hover {
            box-shadow: 0 0 25px var(--primary-color), 0 0 5px rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- Header (Responsivo) -->
        <header class="p-4 md:p-6 bg-secondary-color shadow-lg">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <!-- Logotipo/Lojotipo (Substitua por sua imagem) -->
                <div class="flex items-center space-x-2">
                    <img src="/images/logo_small.png" alt="Escalefy Logo" class="h-8 w-auto hidden sm:block">
                    <div class="text-2xl font-extrabold text-primary-dynamic">Escalefy</div>
                </div>
                <!-- Fim Logotipo -->
                <nav class="space-x-4">
                    <a href="#pricing" class="px-3 py-1 text-sm font-medium text-white hover:text-primary-dynamic transition duration-150">Planos</a>
                    <!-- CORREÇÃO DE TESTE: Usando URL HARCODED (o problema é o servidor, não o código) -->
                    <a href="/login" class="px-3 py-1 text-sm font-medium text-white hover:text-primary-dynamic transition duration-150">Login</a>
                </nav>
            </div>
        </header>

        <!-- Hero Section (Banners e CTA) -->
        <section class="py-16 md:py-24 text-center max-w-4xl mx-auto px-6">
            <!-- Espaço para um BANNER/ILUSTRAÇÃO (Opcional) -->
            <div class="mb-8 max-w-lg mx-auto">
                <img src="/images/hero_banner.svg" alt="Ilustração do Sistema de Rastreamento" class="w-full h-auto opacity-75">
            </div>
            <!-- Fim Banner -->

            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6" style="color: {{ $primary_color }};">
                {{ $settings->hero_title ?? 'Título Principal Faltando' }}
            </h1>
            <p class="text-lg md:text-xl lg:text-2xl text-gray-400 mb-10 max-w-3xl mx-auto">
                {{ $settings->hero_subtitle ?? 'Subtítulo Faltando' }}
            </p>
            <a href="/register" class="btn-cta bg-primary-dynamic text-gray-900 font-bold py-3 md:py-4 px-8 md:px-12 rounded-full text-lg shadow-lg inline-block">
                {{ $settings->cta_button_text ?? 'Começar Agora' }}
            </a>
            <p class="mt-4 text-sm text-gray-500">Teste grátis por 7 dias. Comece a rastrear em 5 minutos.</p>
        </section>

        <!-- Features (Responsivo) -->
        <section class="py-16 md:py-20 bg-gray-900">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-white mb-12">Por que o Escalefy? A Vantagem da Meta Ads API.</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1: Rastreamento Server-Side -->
                    <div class="bg-gray-800 p-6 rounded-xl border border-primary-dynamic/30 hover:border-primary-dynamic transition duration-300">
                        <div class="text-primary-dynamic mb-3 text-3xl font-bold">01</div>
                        <h3 class="text-xl font-bold text-white mb-3">Rastreamento 100% Preciso</h3>
                        <p class="text-gray-400">Integração nativa com a **API de Conversão da Meta (Server-Side)**, garantindo que você não perca eventos devido a bloqueadores de navegador. Seus dados são a sua mina de ouro.</p>
                    </div>
                    <!-- Feature 2: Geração de UTM e Automação -->
                    <div class="bg-gray-800 p-6 rounded-xl border border-primary-dynamic/30 hover:border-primary-dynamic transition duration-300">
                        <div class="text-primary-dynamic mb-3 text-3xl font-bold">02</div>
                        <h3 class="text-xl font-bold text-white mb-3">Automação Inteligente</h3>
                        <p class="text-gray-400">Crie e gerencie links UTM facilmente. Defina **regras automáticas** para pausar, escalar ou otimizar conjuntos de anúncios com base no ROAS em tempo real.</p>
                    </div>
                    <!-- Feature 3: Dashboard de Decisão -->
                    <div class="bg-gray-800 p-6 rounded-xl border border-primary-dynamic/30 hover:border-primary-dynamic transition duration-300">
                        <div class="text-primary-dynamic mb-3 text-3xl font-bold">03</div>
                        <h3 class="text-xl font-bold text-white mb-3">Dashboard Focado em Lucro</h3>
                        <p class="text-gray-400">Visualize ROI, ROAS e CPL lado a lado, cruzando dados de custo da Meta com dados de conversão do seu checkout. Tome decisões baseadas em ciência, não em suposição.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section (DYNAMIC PLANS) -->
        <section id="pricing" class="py-16 md:py-20">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Escolha o seu plano de escala.</h2>
                <p class="text-lg text-gray-400 mb-12">Planos limitados por volume para garantir que você só pague pelo que realmente usa.</p>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($plans as $plan)
                    <div class="bg-gray-800 p-6 md:p-8 rounded-2xl border border-gray-700 shadow-2xl {{ $loop->iteration == 2 ? 'border-primary-dynamic scale-[1.02] shadow-primary-dynamic/50' : '' }} transition duration-500 hover:shadow-primary-dynamic/40">
                        <h3 class="text-2xl font-bold text-white mb-3">{{ $plan->name }}</h3>
                        <p class="text-4xl md:text-5xl font-extrabold mb-2" style="color: {{ $primary_color }};">
                            @if($plan->price == 0)
                                Grátis
                            @else
                                R$ {{ number_format($plan->price, 0, ',', '.') }}<span class="text-lg text-gray-400 font-normal">/mês</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-6">{{ $plan->description ?? 'Ideal para começar a testar o rastreamento.' }}</p>

                        <!-- Botão de Assinatura -->
                        <a href="/register?plan={{ $plan->id }}" class="bg-primary-dynamic text-gray-900 font-bold py-3 px-8 rounded-full w-full block btn-cta mb-8">
                            Assinar {{ $plan->name }}
                        </a>

                        <ul class="text-gray-300 text-left space-y-3">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-1 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>{{ $plan->link_limit }} Links UTM Ativos</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-1 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>{{ number_format($plan->event_limit, 0, '', '.') }} Eventos API/mês</span>
                            </li>
                            @foreach($plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-1 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @empty
                    <p class="text-lg text-red-400 col-span-3">Nenhum plano ativo encontrado. Configure os planos no painel de administração.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="p-6 text-center text-gray-500 border-t border-gray-800 mt-10">
            © 2025 Escalefy. Todos os direitos reservados. Feito para escala.
        </footer>
    </div>
</body>
</html>