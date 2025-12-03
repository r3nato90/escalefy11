<?php
// resources/views/checkout/select-plan.blade.php
// Tela de Escolha de Planos, carregada após Login/Registro (Se a assinatura for TRIAL/PENDING).

use App\Models\Setting;
use App\Models\Plan;

// Carrega as configurações de design
$settings = Setting::getSettings();
$plans = Plan::where('is_active', true)->orderBy('price', 'asc')->get();

$primary_color = $settings->primary_color ?? '#00BFFF';
$secondary_color = $settings->secondary_color ?? '#0f172a'; // Usando o fundo padrão dark
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha seu Plano - Escalefy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: {{ $primary_color }}; }
        body { font-family: 'Urbanist', sans-serif; background-color: {{ $secondary_color }}; color: #f8fafc; }
        .text-primary-dynamic { color: var(--primary-color); }
        .bg-primary-dynamic { background-color: var(--primary-color); }
        .btn-cta {
            box-shadow: 0 0 10px var(--primary-color);
            transition: all 0.3s ease;
        }
        .btn-cta:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px var(--primary-color);
        }
    </style>
</head>
<body class="p-4 md:p-10">

    <div class="max-w-7xl mx-auto text-center py-10 md:py-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3">
            Ative sua Assinatura <span class="text-primary-dynamic">Escalefy</span>
        </h1>
        <p class="text-xl text-gray-400 mb-12">Escolha o plano que melhor se adapta ao seu volume de tráfego Meta Ads para iniciar o rastreamento Server-Side.</p>

        @if(session('error'))
            <div class="max-w-2xl mx-auto bg-red-900/50 p-4 rounded-lg text-red-300 text-sm border border-red-700 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Grid de Planos -->
        <div class="grid grid-cols-1 gap-8 md:grid-cols-{{ count($plans) > 1 ? count($plans) : 2 }} justify-center">
            @forelse($plans as $plan)
                <div class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-2xl transition duration-500 hover:shadow-primary-dynamic/40 
                    {{ $plan->price > 0 && $loop->iteration == 2 ? 'border-primary-dynamic scale-[1.05]' : '' }}">
                    
                    <h3 class="text-2xl font-bold text-white mb-3">{{ $plan->name }}</h3>
                    <p class="text-5xl font-extrabold mb-4" style="color: {{ $primary_color }};">
                        @if($plan->price == 0)
                            Grátis
                        @else
                            R$ {{ number_format($plan->price, 0, ',', '.') }}<span class="text-lg text-gray-400 font-normal">/mês</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500 mb-8">{{ $plan->description ?? 'Plano ideal para o seu início.' }}</p>

                    <!-- CTA para Iniciar Checkout -->
                    <a href="{{ route('checkout.start', ['plan' => $plan->id]) }}" class="bg-primary-dynamic text-gray-900 font-bold py-3 px-8 rounded-full w-full block btn-cta mb-8">
                        @if($plan->price == 0)
                            Começar Grátis (Trial)
                        @else
                            Pagar com PIX
                        @endif
                    </a>

                    <ul class="text-gray-300 text-left space-y-3 text-sm">
                        <!-- Limites -->
                        <li class="flex items-start font-semibold">
                            <svg class="w-5 h-5 mr-2 mt-1 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM6.5 9A1.5 1.5 0 018 7.5h4A1.5 1.5 0 0113.5 9v2A1.5 1.5 0 0112 12.5H8A1.5 1.5 0 016.5 11V9z"></path></svg>
                            <span>{{ $plan->link_limit }} Links UTM e {{ number_format($plan->event_limit, 0, '', '.') }} Eventos API/mês</span>
                        </li>
                        <!-- Features -->
                        @foreach($plan->features as $feature)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-lg text-red-400 col-span-full">Nenhum plano ativo encontrado. Por favor, configure os planos no painel de administração.</p>
            @endforelse
        </div>
        
        <!-- Link para Dashboard (caso o usuário queira voltar) -->
        <div class="mt-10">
            <a href="{{ route('user.dashboard') }}" class="text-gray-500 hover:text-primary-dynamic transition text-sm">
                Voltar para o Dashboard (Você está em status {{ strtoupper(Auth::user()->subscription_status) }})
            </a>
        </div>
    </div>
</body>
</html>