<div class="space-y-8 p-4 md:p-10">
    <h2 class="text-3xl font-bold text-white">Configurações Globais e Integrações</h2>
    <p class="text-gray-400">Configure o design da página de vendas e insira as chaves de API cruciais para o funcionamento do sistema.</p>

    <!-- Mensagens de Feedback -->
    @if (session()->has('message'))
        <div class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg border border-emerald-500/50 shadow-lg">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="updateSettings" class="space-y-6 bg-gray-800 p-8 rounded-xl border border-gray-700 shadow-2xl">
        
        <!-- SEÇÃO: DESIGN E PÁGINA DE VENDAS -->
        <h3 class="text-xl font-semibold text-escalefy border-b border-gray-700 pb-3">Design da Página de Vendas (Hero)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cor Primária -->
            <div>
                <label for="primary_color" class="block text-sm font-medium text-gray-400">Cor Primária (Hex)</label>
                <input type="color" wire:model.live="primary_color" id="primary_color" class="mt-1 block w-full h-10 bg-gray-700 border-gray-600 rounded-md shadow-sm p-1">
                <p class="mt-1 text-sm text-gray-500">Preview: <span class="font-bold" style="color: {{ $primary_color }}">Esta é a cor Escalefy</span></p>
                @error('primary_color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <!-- Texto do Botão CTA -->
            <div>
                <label for="cta_button_text" class="block text-sm font-medium text-gray-400">Texto do Botão CTA</label>
                <input type="text" wire:model="cta_button_text" id="cta_button_text" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Ex: Começar a Escalar Agora">
                @error('cta_button_text') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <!-- Título Principal -->
        <div>
            <label for="hero_title" class="block text-sm font-medium text-gray-400">Título Principal (Hero)</label>
            <input type="text" wire:model="hero_title" id="hero_title" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2">
            @error('hero_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <!-- Subtítulo -->
        <div>
            <label for="hero_subtitle" class="block text-sm font-medium text-gray-400">Subtítulo (Hero)</label>
            <textarea wire:model="hero_subtitle" id="hero_subtitle" rows="3" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2"></textarea>
            @error('hero_subtitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- SEÇÃO: INTEGRAÇÃO LXPAY -->
        <h3 class="text-xl font-semibold text-escalefy border-b border-gray-700 pb-3 pt-6">Integração LXPay (Pagamentos PIX)</h3>
        <p class="text-sm text-gray-500 mb-4">Insira suas chaves de API para processar pagamentos e o token secreto para validar webhooks de pagamento.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Public Key -->
            <div>
                <label for="lxpay_public_key" class="block text-sm font-medium text-gray-400">x-public-key</label>
                <input type="text" wire:model="lxpay_public_key" id="lxpay_public_key" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Sua chave pública LXPay">
                @error('lxpay_public_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <!-- Secret Key -->
            <div>
                <label for="lxpay_secret_key" class="block text-sm font-medium text-gray-400">x-secret-key</label>
                <input type="password" wire:model="lxpay_secret_key" id="lxpay_secret_key" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Sua chave secreta (Mantenha seguro!)">
                @error('lxpay_secret_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Webhook Token -->
        <div class="pt-2">
            <label for="webhook_token" class="block text-sm font-medium text-gray-400">Token Secreto do Webhook LXPay</label>
            <input type="text" wire:model="webhook_token" id="webhook_token" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Ex: SEU_TOKEN_SECRETO_DO_WEBHOOK">
            <p class="text-xs text-gray-500 mt-1">Este token é usado para garantir que o webhook que recebemos veio da LXPay, e não de um invasor.</p>
            @error('webhook_token') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <!-- SEÇÃO: INTEGRAÇÃO META ADS -->
        <h3 class="text-xl font-semibold text-escalefy border-b border-gray-700 pb-3 pt-6">Integração Meta Ads (Pixel/CAPI)</h3>
        <p class="text-sm text-gray-500 mb-4">Insira aqui o ID do Pixel que o Escalefy deve usar para enviar eventos de conversão Server-Side (CAPI).</p>
        
        <!-- Pixel ID -->
        <div>
            <label for="global_pixel_id" class="block text-sm font-medium text-gray-400">ID do Pixel Global da Meta</label>
            <input type="text" wire:model="global_pixel_id" id="global_pixel_id" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Ex: 1234567890">
            <p class="text-xs text-gray-500 mt-1">Este é o Pixel padrão que a plataforma utilizará para a API de Conversão, a menos que o usuário configure um específico.</p>
            <!-- Este campo precisa ser adicionado ao Modelo Setting e ao Livewire Component. -->
        </div>


        <div class="flex justify-end pt-6">
            <button type="submit" class="btn-primary py-2 px-6 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>