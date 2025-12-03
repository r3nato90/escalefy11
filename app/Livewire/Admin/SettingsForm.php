<?php
// app/Livewire/Admin/SettingsForm.php
// Gerencia as configurações globais do sistema e as chaves de API.

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class SettingsForm extends Component
{
    public $settings;
    
    // Design e Vendas
    public $primary_color;
    public $hero_title;
    public $hero_subtitle;
    public $cta_button_text;
    
    // Integração LXPay
    public $lxpay_public_key;
    public $lxpay_secret_key;
    public $webhook_token; 
    
    // Integração Meta Ads
    public $global_pixel_id; 

    // Regras de validação
    protected $rules = [
        'primary_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        'hero_title' => 'required|string|max:255',
        'hero_subtitle' => 'required|string|max:1000',
        'cta_button_text' => 'required|string|max:100',
        'lxpay_public_key' => 'nullable|string|max:255',
        'lxpay_secret_key' => 'nullable|string|max:255',
        'webhook_token' => 'nullable|string|max:255',
        'global_pixel_id' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        // Carrega o registro único de configurações
        $this->settings = Setting::getSettings();
        
        // Atribuição de variáveis públicas
        $this->primary_color = $this->settings->primary_color;
        $this->hero_title = $this->settings->hero_title;
        $this->hero_subtitle = $this->settings->hero_subtitle;
        $this->cta_button_text = $this->settings->cta_button_text;
        $this->lxpay_public_key = $this->settings->lxpay_public_key;
        $this->lxpay_secret_key = $this->settings->lxpay_secret_key;
        $this->webhook_token = $this->settings->webhook_token;
        $this->global_pixel_id = $this->settings->global_pixel_id;
    }

    // Salva as configurações no banco de dados
    public function updateSettings()
    {
        $this->validate();

        try {
            $this->settings->update([
                'primary_color' => $this->primary_color,
                'hero_title' => $this->hero_title,
                'hero_subtitle' => $this->hero_subtitle,
                'cta_button_text' => $this->cta_button_text,
                'lxpay_public_key' => $this->lxpay_public_key,
                'lxpay_secret_key' => $this->lxpay_secret_key,
                'webhook_token' => $this->webhook_token,
                'global_pixel_id' => $this->global_pixel_id,
            ]);

            session()->flash('message', 'Configurações salvas com sucesso! O design da página de vendas e as chaves foram atualizados.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações: ' . $e->getMessage());
            session()->flash('message', 'Erro ao salvar configurações. Verifique os logs.');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings-form');
    }
}