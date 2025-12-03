<?php
// resources/views/checkout/payment.blade.php
// Tela de exibição do PIX, QR Code e Copia e Cola.

use App\Models\Setting;

// Carrega as configurações de design
$settings = Setting::getSettings();
$primary_color = $settings->primary_color ?? '#00BFFF';

// Variáveis de entrada (passadas pelo CheckoutController)
// $plan, $pixCode, $qrCodeImage, $transactionId
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIX - Ativação de Assinatura Escalefy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v3.x.x/dist/alpine.min.js" defer></script>
    <style>
        :root { --primary-color: {{ $primary_color }}; }
        body { font-family: 'Urbanist', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .text-escalefy { color: var(--primary-color); }
        .bg-escalefy { background-color: var(--primary-color); }
        .border-escalefy { border-color: var(--primary-color); }
        /* Estilo para simular um QR Code se a imagem não vier na API */
        .qr-placeholder { 
            background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            height: 250px;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg bg-gray-900 p-6 md:p-8 rounded-xl shadow-2xl border border-gray-700 text-center">
        <h1 class="text-3xl font-extrabold text-white mb-2">Pague seu PIX</h1>
        <p class="text-gray-400 mb-6 text-lg">
            Ativação do plano **{{ $plan->name }}** (R$ {{ number_format($plan->price, 2, ',', '.') }})
        </p>

        <!-- QR Code e Contagem Regressiva (Simulada) -->
        <div class="space-y-4">
            <p class="text-escalefy font-semibold text-xl" x-data="{ time: 900 }" x-init="setInterval(() => { if(time > 0) time--; }, 1000)">
                Expira em: <span x-text="`${Math.floor(time / 60).toString().padStart(2, '0')}:${(time % 60).toString().padStart(2, '0')}`"></span>
            </p>

            <div class="mx-auto w-64 h-64 border-8 border-gray-700 rounded-lg overflow-hidden">
                @if($qrCodeImage)
                    <!-- Exibe a imagem do QR Code se a LXPay fornecer -->
                    <img src="{{ $qrCodeImage }}" alt="QR Code PIX" class="w-full h-full object-cover p-2">
                @elseif($pixCode)
                    <!-- Se não tiver a imagem, mas tiver o código, simula um QR Code e avisa o usuário -->
                    <div class="qr-placeholder w-full h-full p-4">
                        <svg class="w-16 h-16 text-escalefy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <p class="text-gray-400 mt-2 text-sm">QR Code indisponível. Use o Copia e Cola abaixo.</p>
                    </div>
                @else
                    <div class="qr-placeholder w-full h-full p-4">
                        <p class="text-red-400 font-bold">Erro: Nenhum dado PIX recebido.</p>
                    </div>
                @endif
            </div>

            <!-- Botão Copia e Cola -->
            @if($pixCode)
            <div x-data="{ copied: false }" class="mt-8">
                <button 
                    @click="navigator.clipboard.writeText('{{ $pixCode }}'); copied = true; setTimeout(() => copied = false, 3000)"
                    class="w-full bg-escalefy text-gray-900 font-bold py-3 rounded-lg hover:bg-escalefy/90 transition duration-150 flex items-center justify-center space-x-2 btn-copy"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4-4-4m4 4V7"></path></svg>
                    <span x-show="!copied">Copiar Código PIX (Copia e Cola)</span>
                    <span x-show="copied" class="text-green-900 font-extrabold">Copiado para a área de transferência!</span>
                </button>
                <div class="text-xs text-gray-500 mt-2 select-all break-all">{{ $pixCode }}</div>
            </div>
            @endif

            <p class="text-gray-500 text-sm mt-6">
                Assim que o pagamento for confirmado, seu plano **{{ $plan->name }}** será ativado automaticamente.
                <br>Você será redirecionado para o dashboard em instantes.
            </p>
        </div>
        
        <div class="text-xs text-gray-600 mt-8">
            ID da Transação: {{ $transactionId ?? 'N/A' }} | Processado por LXPay
        </div>
    </div>
</body>
</html>