<?php
// app/Livewire/UtmLinkManager.php

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

    protected $rules = [
        'base_url' => 'required|url',
        'utm_source' => 'required|string|max:50',
        'utm_medium' => 'nullable|string|max:50',
        'utm_campaign' => 'nullable|string|max:50',
        'utm_content' => 'nullable|string|max:100', // Aqui incluímos os parâmetros dinâmicos da Meta
    ];

    public function mount()
    {
        $this->loadLinks();
    }

    public function loadLinks()
    {
        $this->links = UtmLink::where('user_id', Auth::id())->latest()->get();
    }

    public function generateUrl()
    {
        $this->validate();

        $shortCode = $this->linkId ? UtmLink::find($this->linkId)->short_code : Str::random(8);

        // Constrói a URL final com parâmetros UTM e parâmetros dinâmicos da Meta
        $urlComponents = parse_url($this->base_url);
        $separator = isset($urlComponents['query']) ? '&' : '?';

        $fullUrl = $this->base_url . $separator . http_build_query(array_filter([
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
        ]));

        if ($this->linkId) {
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
            UtmLink::create([
                'user_id' => Auth::id(),
                'short_code' => $shortCode,
                'full_url' => $fullUrl,
                'utm_source' => $this->utm_source,
                'utm_medium' => $this->utm_medium,
                'utm_campaign' => $this->utm_campaign,
                'utm_content' => $this->utm_content,
            ]);
            $this->successMessage = 'Link UTM gerado com sucesso! Código Curto: ' . $shortCode;
        }

        $this->resetForm();
        $this->loadLinks();
    }
    
    public function resetForm()
    {
        $this->linkId = null;
        $this->base_url = '';
        $this->utm_source = 'facebook';
        $this->utm_medium = 'cpc';
        $this->utm_campaign = '';
        $this->utm_content = '{{ad.id}}'; // Sugestão para rastreamento granular
        $this->resetValidation();
    }

    public function deleteLink(UtmLink $link)
    {
        $link->delete();
        $this->successMessage = 'Link excluído com sucesso.';
        $this->loadLinks();
    }

    public function render()
    {
        return view('livewire.utm-link-manager');
    }
}