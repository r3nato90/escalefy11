<?php
// app/Models/UtmLink.php
// Modelo para armazenar os links UTM gerados e as estatísticas de cliques.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtmLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'short_code',
        'full_url', // URL completa, incluindo todos os parâmetros UTM e dinâmicos
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content', // Usado para armazenar o ID do anúncio (ex: {{ad.id}})
        'clicks',
    ];

    protected $casts = [
        'clicks' => 'integer',
    ];

    /**
     * Relação: Um link UTM pertence a um Usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}