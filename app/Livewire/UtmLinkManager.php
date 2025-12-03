<?php
// app/Livewire/UtmLinkManager.php
// Componente Livewire para a criação e gestão dos Links UTM do usuário.

namespace App\Livewire;

use App\Models\UtmLink;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UtmLinkManager extends Component
{
    public $links;
    public $linkId;
    public $base_url, $utm_source, $utm_medium, $utm_campaign, $utm_content;
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'base_url' => 'required|url',
        'utm_source' => 'required|string|max:50',
        'utm_medium' => 'nullable|string|max:50',
        'utm_campaign' => 'nullable|string|max:50',
        'utm_content' => 'nullable|string|max:100', // Campo para o parâmetro dinâmico da Meta (e.g., {{ad.id}})
    ];

    public function mount()
    {
        // Define valores iniciais no mount
        $this->resetForm();
        $this->loadLinks();
    }

    public function loadLinks()
    {
        // Carrega apenas os links do usuário autenticado
        $this->links = UtmLink::where('user_id', Auth::id())->latest()->get();
    }

    public function generateUrl()
    {
        $this->validate();
        $this->successMessage = '';
        $this->errorMessage = '';

        // Gera um código curto único de 8 caracteres
        $shortCode = $this->linkId ? UtmLink::find($this->linkId)->short_code : Str::random(8);

        // Constrói a URL final com todos os parâmetros UTM
        $urlComponents = parse_url($this->base_url);
        $separator = isset($urlComponents['query']) ? '&' : '?';
        
        // 1. Constrói a query string com os valores, usando urlencode
        $queryString = http_build_query(array_filter([
            'utm_source' => urlencode($this->utm_source),
            'utm_medium' => urlencode($this->utm_medium ?? ''),
            'utm_campaign' => urlencode($this->utm_campaign ?? ''),
            'utm_content' => urlencode($this->utm_content ?? ''),
        ]));

        // 2. Substitui os placeholders de Meta ({{...}}) que foram codificados
        // O Meta Ads precisa que esses placeholders estejam na forma {{ad.id}} e não %7Bad.id%7D
        $fullUrl = $this->base_url . $separator . str_replace(
            ['%7Bad.id%7D', '%7Bcampaign.name%7D', '%7Badset.id%7D'], 
            ['{{ad.id}}', '{{campaign.name}}', '{{adset.id}}'], 
            $queryString
        );

        try {
            if ($this->linkId) {
                // Atualiza um link existente
                $link = UtmLink::find($this->linkId);
                $link->update([
                    'full_url' => $fullUrl,
                    'utm_source' => $this->utm_source,
                    'utm_medium' => $this->utm_medium,
                    'utm_campaign' => $this->utm_campaign,
                    'utm_content' => $this->utm_content,
                ]);
                $this->successMessage = 'Link UTM atualizado com sucesso!';
            } else {
                // Cria um novo link
                UtmLink::create([
                    'user_id' => Auth::id(),
                    'short_code' => $shortCode,
                    'full_url' => $fullUrl,
                    'utm_source' => $this->utm_source,
                    'utm_medium' => $this->utm_medium,
                    'utm_campaign' => $this->utm_campaign,
                    'utm_content' => $this->utm_content,
                ]);
                $this->successMessage = "Link UTM gerado com sucesso! Código Curto: {$shortCode}";
            }

            $this->resetForm();
            $this->loadLinks();

        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao salvar o link: ' . $e->getMessage();
            Log::error('Erro ao gerar URL UTM: ' . $e->getMessage());
        }
    }
    
    public function resetForm()
    {
        $this->linkId = null;
        $this->base_url = '';
        $this->utm_source = 'facebook';
        $this->utm_medium = 'cpc';
        $this->utm_campaign = '{{campaign.name}}'; // Sugestão
        $this->utm_content = '{{ad.id}}'; // Sugestão
        $this->resetValidation();
    }

    public function deleteLink(UtmLink $link)
    {
        // Verifica se o link pertence ao usuário antes de deletar
        if ($link->user_id !== Auth::id()) {
            $this->errorMessage = 'Você não tem permissão para excluir este link.';
            return;
        }
        $link->delete();
        $this->successMessage = 'Link excluído com sucesso.';
        $this->loadLinks();
    }

    public function render()
    {
        return view('livewire.utm-link-manager');
    }
}