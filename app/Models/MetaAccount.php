<?php
// app/Models/MetaAccount.php
// Modelo para armazenar as credenciais e informações de conexão do usuário com o Meta Ads.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meta_user_id',
        'long_lived_token', // Token de acesso de longa duração (crucial para API)
        'account_name',
        'pixel_id', // Para API de Conversão
    ];

    protected $hidden = [
        'long_lived_token', // Não expor o token em dumps/logs
    ];

    /**
     * Relação: Uma conta Meta pertence a um Usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}