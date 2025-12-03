<?php
// app/Http/Controllers/MetaAuthController.php
// Gerencia o fluxo de autenticação OAuth 2.0 com a Meta Ads (Facebook/Instagram).

namespace App\Http\Controllers;

use App\Models\MetaAccount;
use App\Services\MetaAdsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MetaAuthController extends Controller
{
    protected $metaService;

    public function __construct(MetaAdsService $metaService)
    {
        // Injeta o serviço de comunicação com a API da Meta
        $this->metaService = $metaService;
    }

    /**
     * Redireciona o usuário para a página de autorização da Meta.
     */
    public function redirectToMeta()
    {
        Log::info('Iniciando redirecionamento para autenticação Meta.');
        return redirect($this->metaService->getAuthUrl());
    }

    /**
     * Lida com o callback após o usuário autorizar a aplicação na Meta.
     */
    public function handleMetaCallback(Request $request)
    {
        // 1. Verifica se houve erro na autorização (ex: usuário negou)
        if ($request->has('error')) {
            $errorDesc = $request->get('error_description', 'Nenhuma descrição fornecida.');
            Log::warning('Autorização Meta falhou ou foi cancelada.', ['user_id' => Auth::id(), 'error' => $errorDesc]);
            return redirect()->route('user.dashboard')->with('error', 'Autorização Meta cancelada ou falhou: ' . $errorDesc);
        }
        
        // 2. Tenta obter o token de curta duração usando o código (code)
        $code = $request->code;
        $tokenData = $this->metaService->getAccessToken($code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return redirect()->route('user.dashboard')->with('error', 'Falha ao obter token de curta duração do Meta. Verifique as chaves APP_ID/SECRET no .env.');
        }
        $shortLivedToken = $tokenData['access_token'];

        // 3. Obtém o token de longa duração (60 dias)
        $longLivedData = $this->metaService->getLongLivedToken($shortLivedToken);

        if (!$longLivedData || !isset($longLivedData['access_token'])) {
            return redirect()->route('user.dashboard')->with('error', 'Falha ao obter token de longa duração do Meta.');
        }
        $longLivedToken = $longLivedData['access_token'];

        // 4. Obtém dados básicos do usuário (ID e Nome)
        $userData = $this->metaService->getProfileData($longLivedToken);
        $metaUserId = $userData['id'] ?? null;
        $userName = $userData['name'] ?? 'Usuário Meta Desconhecido';

        if (!$metaUserId) {
            return redirect()->route('user.dashboard')->with('error', 'Não foi possível recuperar o ID de usuário da Meta.');
        }

        // 5. Salva/Atualiza a conta Meta no banco de dados para o usuário logado
        MetaAccount::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'meta_user_id' => $metaUserId,
                'long_lived_token' => $longLivedToken,
                'account_name' => $userName,
                // O pixel_id será configurado pelo usuário no dashboard
            ]
        );
        
        Log::info("Conta Meta Ads conectada com sucesso.", ['user_id' => Auth::id(), 'meta_user_id' => $metaUserId]);

        return redirect()->route('user.dashboard')->with('success', 'Conta Meta Ads conectada com sucesso! Você pode agora gerar links rastreáveis.');
    }
}