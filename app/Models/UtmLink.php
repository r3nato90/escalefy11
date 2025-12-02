<?php
// app/Models/UtmLink.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtmLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'short_code',
        'full_url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'clicks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}