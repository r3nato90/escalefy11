<?php
// config/lxpay.php
return [
    'base_url' => 'https://api.lxpay.com.br/api/v1/gateway/pix/receive',
    'public_key' => env('LXPAY_PUBLIC_KEY', 'your_lxpay_public_key'),
    'secret_key' => env('LXPAY_SECRET_KEY', 'your_lxpay_secret_key'),
    // Você pode adicionar outras configurações da LXPay aqui, se necessário
];