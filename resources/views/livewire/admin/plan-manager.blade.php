<div class="space-y-8 p-4 md:p-10">
    <h2 class="text-3xl font-bold text-white">Gerenciamento de Planos (SaaS)</h2>
    <p class="text-gray-400">Defina os tiers de assinatura, limites de uso (links, eventos API) e os recursos inclusos.</p>

    <!-- Mensagens de Feedback -->
    @if (session()->has('message'))
        <div class="bg-emerald-500/20 text-emerald-300 p-4 rounded-lg border border-emerald-500/50 shadow-lg">
            {{ session('message') }}
        </div>
    @endif
    
    <!-- Botão de Ação -->
    <button wire:click="createPlan" class="btn-primary py-2 px-6 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
        + Adicionar Novo Plano
    </button>

    <!-- Lista de Planos (Display em Cards Responsivos) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($plans as $plan)
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl transition duration-300 {{ $plan->is_active ? 'hover:border-escalefy' : 'opacity-70' }}">
                <h3 class="text-xl font-bold text-white mb-2">{{ $plan->name }}</h3>
                <p class="text-3xl font-extrabold text-escalefy mb-4">
                    @if($plan->price == 0)
                        Grátis
                    @else
                        R$ {{ number_format($plan->price, 2, ',', '.') }}<span class="text-base font-normal text-gray-400">/mês</span>
                    @endif
                </p>

                <ul class="space-y-2 text-gray-300 mb-6 text-sm">
                    <li class="flex items-center text-indigo-400 font-medium">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM6.5 9A1.5 1.5 0 018 7.5h4A1.5 1.5 0 0113.5 9v2A1.5 1.5 0 0112 12.5H8A1.5 1.5 0 016.5 11V9z"></path></svg>
                        {{ $plan->link_limit }} Links UTM Ativos
                    </li>
                    <li class="flex items-center text-indigo-400 font-medium">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h14a1 1 0 011 1v4a1 1 0 01-1 1H3a1 1 0 01-1-1v-4zM10 2a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V3a1 1 0 011-1h7z"></path></svg>
                        {{ number_format($plan->event_limit, 0, '', '.') }} Eventos API/mês
                    </li>
                    @foreach($plan->features as $feature)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-1 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                
                <p class="text-sm text-center mb-4 text-gray-500">Status: <span class="{{ $plan->is_active ? 'text-emerald-400' : 'text-red-400' }} font-bold">{{ $plan->is_active ? 'Ativo' : 'Inativo' }}</span></p>

                <div class="flex space-x-3 mt-4">
                    <button wire:click="editPlan({{ $plan->id }})" class="flex-1 py-2 px-4 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">Editar</button>
                    <button wire:click="deletePlan({{ $plan->id }})" wire:confirm="Tem certeza que deseja excluir este plano? Esta ação é irreversível." class="flex-1 py-2 px-4 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">Excluir</button>
                </div>
            </div>
        @empty
            <div class="md:col-span-4 text-center p-10 bg-gray-800 rounded-xl border border-gray-700">
                <p class="text-xl text-gray-400">Nenhum plano cadastrado. Crie o primeiro plano SaaS.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal de Formulário de Plano (Alpine.js/Livewire) -->
    <div x-data="{ open: false }" x-on:open-modal.window="open = ($event.detail === 'plan-form')" x-on:close-modal.window="open = false" x-show="open" 
         class="fixed inset-0 z-50 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="open = false" class="bg-gray-800 p-6 md:p-8 rounded-xl w-full max-w-lg border border-gray-700 shadow-2xl">
            <h3 class="text-2xl font-bold text-white mb-6">{{ $planId ? 'Editar Plano' : 'Criar Novo Plano' }}</h3>
            <form wire:submit.prevent="savePlan" class="space-y-4">
                
                <!-- Nome e Descrição -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400">Nome do Plano</label>
                    <input type="text" wire:model="name" id="name" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Ex: Plano Pro, Agência">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-400">Descrição Curta</label>
                    <textarea wire:model="description" id="description" rows="2" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2"></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Preço e Limites -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-400">Preço Mensal (R$)</label>
                        <input type="number" step="0.01" wire:model="price" id="price" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" min="0">
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="link_limit" class="block text-sm font-medium text-gray-400">Limite de Links UTM</label>
                        <input type="number" wire:model="link_limit" id="link_limit" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" min="1">
                        @error('link_limit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label for="event_limit" class="block text-sm font-medium text-gray-400">Limite de Eventos API/mês</label>
                    <input type="number" wire:model="event_limit" id="event_limit" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" min="1">
                    @error('event_limit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Recursos (Features) -->
                <div>
                    <label for="features_string" class="block text-sm font-medium text-gray-400">Recursos (Um recurso por linha)</label>
                    <textarea wire:model="features_string" id="features_string" rows="4" class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm text-white p-2" placeholder="Ex:&#10;Automação Avançada&#10;Relatórios de ROAS em tempo real"></textarea>
                    @error('features_string') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- Status Ativo -->
                <div class="flex items-center pt-2">
                    <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 text-escalefy rounded border-gray-600 bg-gray-700 focus:ring-escalefy">
                    <label for="is_active" class="ml-2 block text-sm text-gray-400">Plano Ativo (Visível na página de vendas)</label>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" @click="open = false" class="py-2 px-4 rounded-lg text-gray-400 border border-gray-700 hover:bg-gray-700 transition">Cancelar</button>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Salvar Plano</button>
                </div>
            </form>
        </div>
    </div>
</div>