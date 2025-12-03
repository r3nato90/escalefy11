<?php
// app/Models/Plan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'link_limit',
        'event_limit',
        'features',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array', // Armazena a lista de recursos como JSON
    ];

    /**
     * Relação: Um plano pode ter muitos usuários.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}