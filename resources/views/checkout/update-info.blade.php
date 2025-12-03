<?php
// resources/views/checkout/update-info.blade.php
// Formulário para coletar CPF/CNPJ e Telefone do cliente (requisito para gerar PIX).

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

// Carrega as configurações de design
$settings = Setting::getSettings();
$primary_color = $settings->primary_color ?? '#00BFFF';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados de Pagamento - Escalefy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: {{ $primary_color }}; }
        body { font-family: 'Urbanist', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .text-escalefy { color: var(--primary-color); }
        .bg-escalefy { background-color: var(--primary-color); }
        .focus\:ring-escalefy:focus { --tw-ring-color: var(--primary-color); }
        .focus\:border-escalefy:focus { border-color: var(--primary-color); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-gray-900 p-8 rounded-xl shadow-2xl border border-gray-700">
        <h1 class="text-3xl font-extrabold text-white text-center mb-2">Finalizar Checkout</h1>
        <p class="text-gray-400 text-center mb-6 text-sm">
            Para gerar seu PIX de ativação do **{{ $plan->name }}** (R$ {{ number_format($plan->price, 2, ',', '.') }}), precisamos do seu documento.
        </p>
        <p class="text-xs text-gray-500 text-center mb-8">
            A LXPay exige CPF ou CNPJ para emissão de pagamentos.
        </p>

        <!-- Formulário de Coleta de Dados -->
        <form method="POST" action="{{ route('checkout.save-info') }}" class="space-y-5">
            @csrf
            
            <!-- Campo Oculto para Plan ID -->
            <input type="hidden" name="plan_id" value="{{ $plan->id }}">

            <!-- Documento (CPF/CNPJ) -->
            <div>
                <label for="document" class="block text-sm font-medium text-gray-400">CPF ou CNPJ</label>
                <input type="text" name="document" id="document" value="{{ old('document', Auth::user()->document) }}" required autofocus 
                       class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-lg p-2.5 focus:ring-escalefy focus:border-escalefy" 
                       placeholder="Ex: 123.456.789-00">
                @error('document') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Telefone (Opcional, mas recomendado para LXPay) -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-400">Telefone com DDD (Opcional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                       class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-lg p-2.5 focus:ring-escalefy focus:border-escalefy" 
                       placeholder="Ex: 11988887777">
                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-escalefy text-gray-900 font-bold py-3 rounded-lg hover:bg-escalefy/90 transition duration-150">
                Salvar Dados e Gerar PIX
            </button>
        </form>

        <div class="text-center text-xs text-gray-500 mt-6">
            Seus dados são criptografados e enviados apenas para a processadora de pagamento LXPay.
        </div>
    </div>
</body>
</html>