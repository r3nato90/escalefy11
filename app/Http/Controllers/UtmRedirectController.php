<?php
// app/Http/Controllers/UtmRedirectController.php

namespace App\Http\Controllers;

use App\Models\UtmLink;
use Illuminate\Http\Request;

class UtmRedirectController extends Controller
{
    /**
     * Incrementa o clique e redireciona para a URL completa.
     * @param string $shortCode O código curto do link.
     */
    public function redirect(Request $request, $shortCode)
    {
        $link = UtmLink::where('short_code', $shortCode)->first();

        if (!$link) {
            // Pode redirecionar para uma página 404 customizada ou para a home
            return redirect('/')->with('error', 'Link de rastreamento não encontrado.');
        }

        // 1. Incrementa o contador de cliques (Lógica básica de rastreamento)
        $link->increment('clicks');

        // 2. Redireciona para a URL completa.
        return redirect()->to($link->full_url);
    }
}