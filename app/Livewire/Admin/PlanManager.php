<?php
// app/Livewire/Admin/PlanManager.php
// Gerencia a criação, edição e exclusão dos Planos de Assinatura do SaaS.

namespace App\Livewire\Admin;

use App\Models\Plan;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class PlanManager extends Component
{
    public $plans;
    public $planId;
    public $name, $description, $price, $link_limit, $event_limit, $features_string, $is_active = true;

    // Regras de validação para os campos do formulário
    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'link_limit' => 'required|integer|min:0',
        'event_limit' => 'required|integer|min:0',
        'features_string' => 'required|string',
        'is_active' => 'boolean',
        'description' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->loadPlans();
    }

    public function loadPlans()
    {
        // Carrega todos os planos ordenados por preço
        $this->plans = Plan::orderBy('price', 'asc')->get();
    }

    // Prepara o formulário para editar um plano existente
    public function editPlan(Plan $plan)
    {
        $this->resetForm();
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->link_limit = $plan->link_limit;
        $this->event_limit = $plan->event_limit;
        // Converte o array de recursos para uma string de texto separada por linha para edição
        $this->features_string = implode("\n", $plan->features ?? []);
        $this->is_active = $plan->is_active;

        $this->dispatch('open-modal', 'plan-form');
    }

    // Prepara o formulário para criar um novo plano
    public function createPlan()
    {
        $this->resetForm();
        $this->dispatch('open-modal', 'plan-form');
    }

    // Salva ou atualiza um plano no banco de dados
    public function savePlan()
    {
        $this->validate();

        // Converte a string de recursos separada por linha em um array (JSON)
        $featuresArray = array_map('trim', explode("\n", $this->features_string));
        $featuresArray = array_filter($featuresArray); // Remove linhas vazias

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'link_limit' => $this->link_limit,
            'event_limit' => $this->event_limit,
            'features' => $featuresArray,
            'is_active' => $this->is_active,
        ];

        if ($this->planId) {
            Plan::find($this->planId)->update($data);
            session()->flash('message', 'Plano atualizado com sucesso!');
        } else {
            Plan::create($data);
            session()->flash('message', 'Plano criado com sucesso!');
        }

        $this->loadPlans();
        $this->resetForm();
        $this->dispatch('close-modal', 'plan-form');
    }

    // Exclui um plano
    public function deletePlan(Plan $plan)
    {
        try {
            // Em produção, você deveria verificar se há usuários ativos neste plano antes de deletar
            $plan->delete();
            session()->flash('message', 'Plano excluído com sucesso.');
            $this->loadPlans();
        } catch (\Exception $e) {
            Log::error('Erro ao excluir plano: ' . $e->getMessage());
            session()->flash('message', 'Erro ao excluir o plano. Verifique logs.');
        }
    }

    // Reseta o estado do formulário
    public function resetForm()
    {
        $this->planId = null;
        $this->name = '';
        $this->description = '';
        $this->price = 0;
        $this->link_limit = 100;
        $this->event_limit = 5000;
        $this->features_string = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.plan-manager');
    }
}