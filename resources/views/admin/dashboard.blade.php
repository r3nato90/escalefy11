<x-app-layout>
    <!-- O x-app-layout utiliza o layout em resources/views/layouts/app.blade.php -->
    <div class="py-4 md:py-12">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl font-extrabold text-white mb-6">Visão Geral do Escalefy (Admin)</h1>
            <p class="text-gray-400 mb-8">Bem-vindo, Administrador! Utilize este painel para gerenciar as configurações da plataforma e os planos de assinatura.</p>
            
            <!-- Cards de Métricas (Simuladas) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <!-- Card 1: Usuários Ativos -->
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl transition duration-300 hover:border-escalefy">
                    <p class="text-lg text-gray-400">Usuários Ativos</p>
                    <p class="text-4xl font-extrabold text-escalefy mt-1">1,245</p>
                    <p class="text-sm text-gray-500 mt-2">+12% vs. Mês Anterior</p>
                </div>
                <!-- Card 2: MRR Projetado -->
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl transition duration-300 hover:border-escalefy">
                    <p class="text-lg text-gray-400">MRR Projetado</p>
                    <p class="text-4xl font-extrabold text-escalefy mt-1">R$ 45.7K</p>
                    <p class="text-sm text-gray-500 mt-2">Próxima meta: R$ 50K</p>
                </div>
                <!-- Card 3: Contas Meta Conectadas -->
                <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl transition duration-300 hover:border-escalefy">
                    <p class="text-lg text-gray-400">Contas Meta Conectadas</p>
                    <p class="text-4xl font-extrabold text-escalefy mt-1">850</p>
                    <p class="text-sm text-gray-500 mt-2">Importante para a saúde do CAPI</p>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="mt-10 bg-gray-900/50 p-6 rounded-xl border border-gray-800">
                <h2 class="text-2xl font-bold text-white mb-4 border-b border-gray-700 pb-2">Ações Rápidas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.plans') }}" class="bg-indigo-600 p-4 rounded-lg text-white font-semibold hover:bg-indigo-700 transition text-center shadow-md">
                        Gerenciar Planos SaaS
                    </a>
                    <a href="{{ route('admin.settings') }}" class="bg-indigo-600 p-4 rounded-lg text-white font-semibold hover:bg-indigo-700 transition text-center shadow-md">
                        Configurar Integrações (LXPay/Meta)
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="bg-indigo-600 p-4 rounded-lg text-white font-semibold hover:bg-indigo-700 transition text-center shadow-md">
                        Ver Dashboard de Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>