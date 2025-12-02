<div class="space-y-8 p-4 md:p-8">
    <h2 class="text-3xl font-bold text-white">Gerador e Rastreamento de Links UTM</h2>

    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg border border-emerald-500/50 shadow-lg transition duration-300">
            {{ $successMessage }}
        </div>
    @endif

    <!-- Formulário de Geração -->
    <div class="bg-gray-800 p-6 md:p-8 rounded-xl border border-gray-700 shadow-2xl">
        <h3 class="text-xl font-semibold text-escalefy mb-4">Crie um Novo Link Rastreável</h3>
        <form wire:submit.prevent="generateUrl" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="md:col-span-3 lg:col-span-3">
                <label for="base_url" class="block text-sm font-medium text-gray-400">URL de Destino (Ex: https://seusite.com/checkout)</label>
                <input type="url" wire:model="base_url" id="base_url" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5 focus:ring-escalefy focus:border-escalefy" placeholder="URL para onde o tráfego será enviado">
                @error('base_url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="utm_source" class="block text-sm font-medium text-gray-400">Source (utm\_source)</label>
                <input type="text" wire:model="utm_source" id="utm_source" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="facebook">
                @error('utm_source') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="utm_campaign" class="block text-sm font-medium text-gray-400">Campaign (utm\_campaign)</label>
                <input type="text" wire:model="utm_campaign" id="utm_campaign" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="lancamento_inverno">
                @error('utm_campaign') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="utm_content" class="block text-sm font-medium text-gray-400">Content (utm\_content) - Parâmetro Dinâmico Meta</label>
                <input type="text" wire:model="utm_content" id="utm_content" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2.5" placeholder="{{ad.id}} (Recomendado para rastreamento granular)">
                @error('utm_content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-3 lg:col-span-3 pt-4 flex justify-end">
                <button type="submit" class="btn-primary py-2.5 px-8 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700">
                    Gerar Link Rastreável
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
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Código Curto</th>
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
                            <a href="{{ url($link->short_code) }}" target="_blank" class="hover:underline">{{ url($link->short_code) }}</a>
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
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
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