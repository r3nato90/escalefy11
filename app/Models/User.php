<?php
// app/Models/User.php
// Modelo padrão do usuário com adições para SaaS (admin, assinatura, integrações, LXPay e Socialite).

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'document', // Campo LXPay: CPF/CNPJ
        'phone',    // Campo LXPay: Telefone
        'password',
        'is_admin',
        'subscription_status', 
        'plan_id',
        'social_id',        // Campo Socialite
        'social_provider',  // Campo Socialite
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    // Relações do Escalefy:

    public function metaAccount()
    {
        return $this->hasOne(MetaAccount::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function utmLinks()
    {
        return $this->hasMany(UtmLink::class);
    }
}