<?php
// app/Services/MetaAdsService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MetaAdsService
{
    protected $client;
    protected $appId;
    protected $appSecret;
    protected $redirectUri;
    protected $baseUrl = 'https://graph.facebook.com/v19.0/';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseUrl]);
        $this->appId = config('app.meta_app_id');
        $this->appSecret = config('app.meta_app_secret');
        $this->redirectUri = config('app.meta_redirect_uri');
    }

    /**
     * Gera a URL de autenticação da Meta.
     */
    public function getAuthUrl()
    {
        $scopes = 'ads_read, ads_management, pages_read_engagement, business_management, public_profile'; // Adicionado public_profile
        return "https://www.facebook.com/v19.0/dialog/oauth?" . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $scopes,
            'response_type' => 'code',
        ]);
    }

    /**
     * Troca o código temporário por um token de acesso de curta duração.
     */
    public function getAccessToken($code)
    {
        try {
            $response = $this->client->get('oauth/access_token', [
                'query' => [
                    'client_id' => $this->appId,
                    'redirect_uri' => $this->redirectUri,
                    'client_secret' => $this->appSecret,
                    'code' => $code,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Meta Auth Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Troca o token de curta duração por um token de longa duração (60 dias).
     */
    public function getLongLivedToken($shortLivedToken)
    {
        try {
            $response = $this->client->get('oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $this->appId,
                    'client_secret' => $this->appSecret,
                    'fb_exchange_token' => $shortLivedToken,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Meta Long Lived Token Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém informações básicas do usuário Meta.
     */
    public function getProfileData(string $accessToken)
    {
        try {
            $response = $this->client->get('me', [
                'query' => [
                    'fields' => 'id,name',
                    'access_token' => $accessToken,
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Meta Profile Data Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Envia eventos de conversão (API de Conversão)
     * @param string $pixelId ID do Pixel da Meta
     * @param string $accessToken Token de acesso (do usuário ou do sistema)
     * @param array $events Array de eventos para enviar
     * @return array|null
     */
    public function sendConversionEvents(string $pixelId, string $accessToken, array $events)
    {
        try {
            $response = $this->client->post("{$pixelId}/events", [
                'query' => [
                    'access_token' => $accessToken,
                ],
                'json' => [
                    'data' => $events,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar eventos de conversão para Pixel {$pixelId}: " . $e->getMessage());
            return null;
        }
    }

    // Métodos para puxar dados da API de Marketing (gastos, ROAS, etc.) seriam adicionados aqui
}