<?php
// routes/api.php
// Arquivo dedicado a endpoints de API, geralmente sem sessão (state-less).

use App\Http\Controllers\LxpayWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Estas rotas são tipicamente usadas por serviços externos para se comunicar
| com o Escalefy (ex: Webhooks, Mobile Apps).
|
*/

// Rota para o Webhook da LXPay
// Esta rota não usa o middleware 'auth' e deve estar sempre acessível para a LXPay.
// A autenticação é feita internamente pelo 'LxpayWebhookController' via token secreto.
Route::post('/webhooks/lxpay', [LxpayWebhookController::class, 'handle'])->name('webhooks.lxpay');