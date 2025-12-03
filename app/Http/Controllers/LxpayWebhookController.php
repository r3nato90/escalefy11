<?php
// app/Http/Controllers/LxpayWebhookController.php
// Gerencia as notificações de Webhook da LXPay para ativação/desativação de assinaturas.

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

class LxpayWebhookController extends Controller
{
    /**
     * Lida com as notificações de Webhook da LXPay.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // 1. Obter o token secreto (para validação de segurança)
        // O token deve ser configurado pelo Admin no painel de configurações.
        $expectedToken = Setting::getSettings()->webhook_token ?? 'SEU_TOKEN_SECRETO_DO_WEBHOOK';
        $payload = $request->all();
        $eventToken = $payload['token'] ?? null;

        // Validação de segurança: verifica se o token corresponde ao esperado
        if ($eventToken !== $expectedToken || $expectedToken === 'SEU_TOKEN_SECRETO_DO_WEBHOOK') {
            Log::warning('Tentativa de Webhook LXPay não autorizado.', ['ip' => $request->ip(), 'token' => $eventToken]);
            return response()->json(['message' => 'Token de webhook inválido ou não configurado'], 401);
        }

        $eventType = $payload['event'] ?? 'UNKNOWN';
        
        // Assumimos que o externalRef é o ID do Usuário (user_id) no seu sistema
        $externalRef = $payload['transaction']['externalRef'] ?? null;
        $transactionStatus = $payload['transaction']['status'] ?? null;
        
        // Tentativa de conversão para ID do Usuário
        $userId = (int) $externalRef;
        $user = User::find($userId);

        Log::info("Webhook LXPay recebido. Tipo: {$eventType}, External Ref: {$externalRef}, Status: {$transactionStatus}");


        if (!$user) {
            Log::error("Usuário não encontrado para External Ref (user_id): {$userId}");
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        try {
            DB::beginTransaction(); // Inicia transação para garantir atomicidade

            switch ($eventType) {
                case 'TRANSACTION_PAID':
                    // Evento disparado quando o pagamento é confirmado
                    if ($transactionStatus === 'COMPLETED') {
                        $this->activateSubscription($user, $payload);
                    }
                    break;

                case 'TRANSACTION_REFUNDED':
                case 'TRANSACTION_CHARGED_BACK':
                    // Eventos que exigem a desativação da assinatura
                    $this->deactivateSubscription($user, $transactionStatus);
                    break;
                
                // Você pode adicionar tratamento para 'TRANSACTION_CANCELED', 'PENDING', etc.
            }

            DB::commit(); // Confirma a transação
            // OBRIGATÓRIO: Retornar status 200 para a LXPay não tentar reenviar
            return response()->json(['message' => 'Webhook processado com sucesso'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Reverte em caso de falha
            Log::error('Erro fatal ao processar webhook LXPay:', ['error' => $e->getMessage(), 'user_id' => $userId, 'payload' => $payload]);
            // Retorna 500 para forçar a LXPay a tentar reenviar mais tarde
            return response()->json(['message' => 'Erro interno do servidor'], 500); 
        }
    }

    /**
     * Ativa a assinatura do usuário e registra o plano.
     */
    protected function activateSubscription(User $user, array $payload)
    {
        // Lógica de ativação: 
        // Em um sistema real, você extrairia o ID do plano do payload e o associaria ao usuário.
        // Assumindo que o pagamento é válido para ativar o acesso:
        
        // Nota: A LXPay usa 'externalRef' para identificar o pedido. Usamos o user_id.
        $user->subscription_status = 'active';
        // $user->plan_id = $planId; // Se o ID do plano estivesse em metadata
        $user->save();
        Log::info("Pagamento confirmado para o usuário: {$user->id}. Plano ativado.");
    }

    /**
     * Desativa a assinatura do usuário.
     */
    protected function deactivateSubscription(User $user, string $reason)
    {
        $user->subscription_status = 'inactive';
        $user->save();
        Log::info("Assinatura do usuário {$user->id} desativada. Motivo: {$reason}");
    }
}