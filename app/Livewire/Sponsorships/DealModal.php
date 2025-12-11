<?php

namespace App\Livewire\Sponsorships;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sponsorship;

class DealModal extends Component
{
    public $dealId;
    public $deal;
    
    // Form fields
    public $company_name;
    public $decision_maker_name;
    public $decision_maker_email;
    public $tier;
    public $value;
    public $stage;
    public $probability;
    public $priority;
    public $source;
    public $proposal_sent_date;
    public $contract_signed_date;
    public $loss_reason;
    public $notes;

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'decision_maker_name' => 'nullable|string|max:255',
        'decision_maker_email' => 'nullable|email|max:255',
        'tier' => 'required|in:Platinum,Gold,Silver,Bronze,In-Kind',
        'value' => 'required|numeric|min:0',
        'stage' => 'required|string',
        'probability' => 'required|integer|min:0|max:100',
        'priority' => 'required|in:Hot,Warm,Cold',
        'source' => 'required|string',
        'proposal_sent_date' => 'nullable|date',
        'contract_signed_date' => 'nullable|date',
        'loss_reason' => 'nullable|string',
        'notes' => 'nullable|string',
    ];

    public function mount($dealId = null)
    {
        $this->loadDeal($dealId);
    }

    public function loadDeal($dealId = null)
    {
        // Reset all fields first
        $this->reset([
            'dealId', 'deal', 'company_name', 'decision_maker_name', 
            'decision_maker_email', 'tier', 'value', 'stage', 'probability',
            'priority', 'source', 'proposal_sent_date', 'contract_signed_date',
            'loss_reason', 'notes'
        ]);

        if ($dealId) {
            // Edit mode
            $this->deal = Sponsorship::findOrFail($dealId);
            $this->dealId = $dealId;
            
            // Populate form fields
            $this->company_name = $this->deal->company_name;
            $this->decision_maker_name = $this->deal->decision_maker_name;
            $this->decision_maker_email = $this->deal->decision_maker_email;
            $this->tier = $this->deal->tier;
            $this->value = $this->deal->value;
            $this->stage = $this->deal->stage;
            $this->probability = $this->deal->probability;
            $this->priority = $this->deal->priority;
            $this->source = $this->deal->source;
            $this->proposal_sent_date = $this->deal->proposal_sent_date?->format('Y-m-d');
            $this->contract_signed_date = $this->deal->contract_signed_date?->format('Y-m-d');
            $this->loss_reason = $this->deal->loss_reason;
            $this->notes = $this->deal->notes;
        } else {
            // Create mode - set defaults
            $this->tier = 'Bronze';
            $this->stage = 'Prospect Identification';
            $this->probability = 10;
            $this->priority = 'Warm';
            $this->source = 'Direct';
            $this->value = 0;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->dealId) {
            // Update existing deal
            $this->deal->update([
                'company_name' => $this->company_name,
                'decision_maker_name' => $this->decision_maker_name,
                'decision_maker_email' => $this->decision_maker_email,
                'tier' => $this->tier,
                'value' => $this->value,
                'stage' => $this->stage,
                'priority' => $this->priority,
                'source' => $this->source,
                'proposal_sent_date' => $this->proposal_sent_date,
                'contract_signed_date' => $this->contract_signed_date,
                'loss_reason' => $this->loss_reason,
                'notes' => $this->notes,
            ]);

            $this->deal->logActivity('note', 'Deal updated');
        } else {
            // Create new deal
            Sponsorship::create([
                'user_id' => auth()->id(),
                'company_name' => $this->company_name,
                'decision_maker_name' => $this->decision_maker_name,
                'decision_maker_email' => $this->decision_maker_email,
                'tier' => $this->tier,
                'value' => $this->value,
                'stage' => $this->stage,
                'priority' => $this->priority,
                'source' => $this->source,
                'proposal_sent_date' => $this->proposal_sent_date,
                'contract_signed_date' => $this->contract_signed_date,
                'loss_reason' => $this->loss_reason,
                'notes' => $this->notes,
            ]);
        }

        session()->flash('success', 'Deal saved successfully!');
        $this->dispatch('deal-updated');
        $this->dispatch('close-modal');
    }

    public function close()
    {
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.sponsorships.deal-modal');
    }
}
