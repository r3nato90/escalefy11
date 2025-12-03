<?php
// app/Models/Setting.php
// Modelo para armazenar as configurações globais da plataforma Escalefy.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Define que esta é uma tabela de configuração única (ID=1)
    protected $table = 'settings';
    
    protected $fillable = [
        // Configurações de Design e Página de Vendas
        'primary_color',
        'secondary_color',
        'hero_title',
        'hero_subtitle',
        'cta_button_text',

        // Chaves de Integração LXPay
        'lxpay_public_key',
        'lxpay_secret_key',
        'webhook_token', // Adicionado para a segurança do webhook
        
        // Conteúdo da página de vendas (seções extras)
        'sales_page_content',
    ];

    protected $casts = [
        'sales_page_content' => 'array',
    ];

    /**
     * Método de conveniência para buscar o único registro de configurações,
     * criando-o com valores padrão se não for encontrado.
     * @return \App\Models\Setting
     */
    public static function getSettings()
    {
        // Garante que o registro exista, criando-o se não for encontrado (id=1)
        return self::firstOrCreate(['id' => 1]);
    }
}