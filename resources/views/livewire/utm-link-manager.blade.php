<div class="space-y-8 p-4 md:p-8">
    <h2 class="text-3xl font-bold text-white">Gerador e Rastreamento de Links UTM</h2>
    <p class="text-gray-400">Aqui você gera links rastreáveis, otimizados com parâmetros dinâmicos da Meta, para alimentar o Pixel via API de Conversão.</p>
    
    <!-- CONEXÃO META ADS STATUS -->
    @php
        $metaAccount = Auth::user()->metaAccount;
    @endphp
    
    @if (!$metaAccount)
        <!-- Alerta para conectar a conta Meta -->
        <div class="bg-yellow-500/20 text-yellow-300 p-4 rounded-lg border border-yellow-500/50 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <p class="mb-3 sm:mb-0"><strong>Atenção:</strong> Sua conta Meta Ads não está conectada. Conecte para garantir o rastreamento Server-Side e as automações!</p>
            <a href="{{ route('meta.redirect') }}" class="bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-yellow-700 transition flex-shrink-0 w-full sm:w-auto text-center">
                Conectar Meta Ads
            </a>
        </div>
    @else
        <!-- Alerta de sucesso com informações da conta -->
        <div class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg border border-emerald-500/50 shadow-lg">
            Conta Meta Ads conectada como: <strong>{{ $metaAccount->account_name }}</strong>. Pixel ID: <strong>{{ $metaAccount->pixel_id ?? 'PENDENTE' }}</strong>.
            @if (!$metaAccount->pixel_id)
                <p class="text-sm mt-1">Por favor, configure o ID do Pixel nas configurações da sua conta.</p>
            @endif
        </div>
    @endif

    <!-- Mensagens de Feedback do Livewire -->
    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg border border-emerald-500/50 shadow-lg transition duration-300">
            {{ $successMessage }}
        </div>
    @endif
    @if ($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-red-500/20 text-red-300 p-4 rounded-lg border border-red-500/50 shadow-lg transition duration-300">
            {{ $errorMessage }}
        </div>
    @endif

    <!-- Formulário de Geração -->
    <div class="bg-gray-800 p-6 md:p-8 rounded-xl border border-gray-700 shadow-2xl">
        <h3 class="text-xl font-semibold text-escalefy mb-4">Crie um Novo Link Rastreável</h3>
        <form wire:submit.prevent="generateUrl" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- URL de Destino -->
            <div class="md:col-span-3 lg:col-span-3">
                <label for="base_url" class="block text-sm font-medium text-gray-400">URL de Destino (Ex: https://seusite.com/checkout)</label>
                <input type="url" wire:model.live="base_url" id="base_url" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5 focus:ring-escalefy focus:border-escalefy" placeholder="URL para onde o tráfego será enviado">
                @error('base_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <!-- utm_source -->
            <div>
                <label for="utm_source" class="block text-sm font-medium text-gray-400">Source (utm\_source)</label>
                <input type="text" wire:model.live="utm_source" id="utm_source" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="facebook">
                @error('utm_source') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- utm_campaign -->
            <div>
                <label for="utm_campaign" class="block text-sm font-medium text-gray-400">Campaign (utm\_campaign)</label>
                <input type="text" wire:model.live="utm_campaign" id="utm_campaign" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="{{campaign.name}}">
                @error('utm_campaign') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- utm_content -->
            <div>
                <label for="utm_content" class="block text-sm font-medium text-gray-400">Content (utm\_content) - Parâmetro Dinâmico Meta</label>
                <input type="text" wire:model.live="utm_content" id="utm_content" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="{{ad.id}} (Recomendado)">
                @error('utm_content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <!-- Pré-visualização da URL -->
            <div class="md:col-span-3 lg:col-span-3 bg-gray-700 p-3 rounded-md text-gray-400 text-sm break-all">
                <p class="font-semibold text-white mb-1">Preview do Link Rastreável:</p>
                @if($base_url && $utm_source)
                    {{ url('/') }}/{{ $linkId ? UtmLink::find($linkId)->short_code : 'novo_link_curto' }} <span class="text-escalefy"> -> Redireciona para -> </span> {{ $base_url }}?utm_source={{ $utm_source }}{{ $utm_campaign ? '&utm_campaign=' . $utm_campaign : '' }}{{ $utm_content ? '&utm_content=' . $utm_content : '' }}
                @else
                    <span class="italic text-gray-500">Preencha os campos obrigatórios para gerar o preview.</span>
                @endif
            </div>

            <!-- Botão de Geração -->
            <div class="md:col-span-3 lg:col-span-3 pt-4 flex justify-end">
                <button type="submit" class="btn-primary py-2.5 px-8 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700">
                    {{ $linkId ? 'Atualizar Link' : 'Gerar Link Rastreável' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Tabela de Links Gerados -->
    <div class="bg-gray-800 p-6 md:p-8 rounded-xl border border-gray-700 shadow-2xl overflow-x-auto">
        <h3 class="text-xl font-semibold text-escalefy mb-4">Seus Links Rastreáveis ({{ $links->count() }})</h3>
        
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Link Curto</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider hidden sm:table-cell">Campanha</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider hidden md:table-cell">URL Completa</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cliques</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse ($links as $link)
                    <tr class="hover:bg-gray-700/50 transition duration-150">
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-escalefy">
                            <!-- Link Curto para o usuário copiar -->
                            <div x-data="{ copied: false }">
                                <span class="cursor-pointer" @click="navigator.clipboard.writeText('{{ url($link->short_code) }}'); copied = true; setTimeout(() => copied = false, 2000)">
                                    {{ url($link->short_code) }}
                                </span>
                                <span x-show="copied" class="text-xs text-emerald-400 ml-2">Copiado!</span>
                            </div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-300 hidden sm:table-cell">
                            {{ $link->utm_campaign ?? '-' }}
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-400 max-w-xs truncate hidden md:table-cell" title="{{ $link->full_url }}">
                            {{ $link->full_url }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $link->clicks }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button wire:click="deleteLink({{ $link->id }})" class="text-red-500 hover:text-red-700 transition">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-gray-400">Nenhum link rastreável encontrado. Comece a gerar acima!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>