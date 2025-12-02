<?php
// app/Http/Controllers/MetaAuthController.php

namespace App\Http\Controllers;

use App\Models\MetaAccount;
use App\Services\MetaAdsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaAuthController extends Controller
{
    protected $metaService;

    public function __construct(MetaAdsService $metaService)
    {
        $this->metaService = $metaService;
    }

    /**
     * Redireciona o usuário para a página de autorização da Meta.
     */
    public function redirectToMeta()
    {
        return redirect($this->metaService->getAuthUrl());
    }

    /**
     * Lida com o callback após o usuário autorizar no Meta.
     */
    public function handleMetaCallback(Request $request)
    {
        // 1. Verificar erro
        if ($request->has('error')) {
            return redirect()->route('user.dashboard')->with('error', 'Autorização Meta cancelada ou falhou: ' . $request->get('error_description'));
        }

        // 2. Obter token de curta duração
        $tokenData = $this->metaService->getAccessToken($request->code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return redirect()->route('user.dashboard')->with('error', 'Falha ao obter token de curta duração do Meta.');
        }
        $shortLivedToken = $tokenData['access_token'];

        // 3. Obter token de longa duração (60 dias)
        $longLivedData = $this->metaService->getLongLivedToken($shortLivedToken);

        if (!$longLivedData || !isset($longLivedData['access_token'])) {
            return redirect()->route('user.dashboard')->with('error', 'Falha ao obter token de longa duração do Meta.');
        }
        $longLivedToken = $longLivedData['access_token'];


        // 4. Obter dados do usuário (Meta ID)
        $userData = $this->metaService->getProfileData($longLivedToken); // Método a ser implementado no MetaAdsService
        $metaUserId = $userData['id'] ?? null;
        $userName = $userData['name'] ?? 'Usuário Meta';


        if (!$metaUserId) {
            return redirect()->route('user.dashboard')->with('error', 'Não foi possível recuperar o ID de usuário da Meta.');
        }

        // 5. Salvar/Atualizar conta Meta no banco de dados
        MetaAccount::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'meta_user_id' => $metaUserId,
                'long_lived_token' => $longLivedToken,
                'account_name' => $userName,
                // O pixel_id será adicionado pelo usuário no dashboard
            ]
        );

        return redirect()->route('user.dashboard')->with('success', 'Conta Meta Ads conectada com sucesso! Prossiga para configurar seu Pixel.');
    }
}