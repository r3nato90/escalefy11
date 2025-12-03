<?php
// database/migrations/2025_12_03_000000_add_lxpay_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos necessários para o pagamento PIX (LXPay) e Socialite.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campos de Cliente LXPay (para pagamentos PIX)
            $table->string('document')->nullable()->after('email'); // CPF/CNPJ
            $table->string('phone')->nullable()->after('document'); // Telefone (opcional)
            
            // Campos Socialite (para Login com Google/GitHub)
            $table->string('social_id')->nullable()->after('password');
            $table->string('social_provider')->nullable()->after('social_id');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['document', 'phone', 'social_id', 'social_provider']);
        });
    }
};