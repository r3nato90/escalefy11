<?php
// app/Http/Controllers/UtmRedirectController.php
// Responsável por capturar o clique no link curto, incrementar o contador
// e redirecionar o usuário para a URL de destino completa.

namespace App\Http\Controllers;

use App\Models\UtmLink;
use Illuminate\Http\Request;

class UtmRedirectController extends Controller
{
    /**
     * Captura o código curto (shortCode), incrementa o clique e redireciona
     * para a URL completa rastreável.
     *
     * @param string $shortCode O código curto do link (ex: /abc123xyz)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request, $shortCode)
    {
        // 1. Busca o link pelo código curto
        $link = UtmLink::where('short_code', $shortCode)->first();

        if (!$link) {
            // Se o link não for encontrado, redireciona para a home page
            return redirect('/')->with('error', 'Link de rastreamento não encontrado.');
        }

        // 2. Incrementa o contador de cliques
        // Em um sistema real, essa ação poderia ser enviada para uma fila
        // para não atrasar o redirecionamento, mas a lógica básica é esta:
        $link->increment('clicks');

        // 3. Redireciona para a URL completa
        return redirect()->to($link->full_url);
    }
}