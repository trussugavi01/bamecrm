<?php

namespace App\Livewire\Sponsorships;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\Sponsorship;

#[Layout('layouts.app')]
#[Title('Sponsorship Kanban - B.A.M.E CRM')]
class Index extends Component
{
    public $selectedDeal = null;
    public $showModal = false;

    // Active stages (excluding won/lost)
    public $stages = [
        'Prospect Identification',
        'Initial Outreach',
        'Qualification & Discovery',
        'Proposal Development',
        'Negotiation',
        'Contract & Commitment',
    ];

    public function mount()
    {
        // Initialize
    }

    public function openDeal($dealId)
    {
        $this->selectedDeal = $dealId;
        $this->showModal = true;
    }

    public function openCreateModal()
    {
        \Log::info('openCreateModal called');
        $this->selectedDeal = null;
        $this->showModal = true;
        \Log::info('showModal set to: ' . $this->showModal);
    }

    #[On('close-modal')]
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDeal = null;
    }

    #[On('deal-updated')]
    public function refreshDeals()
    {
        // This will trigger a re-render
    }

    public function updateStage($dealId, $newStage)
    {
        $deal = Sponsorship::find($dealId);
        
        if (!$deal) {
            return;
        }

        // Validate stage transition
        $errors = $deal->canMoveToStage($newStage);
        
        if (!empty($errors)) {
            session()->flash('error', implode(' ', $errors));
            return;
        }

        $oldStage = $deal->stage;
        $deal->stage = $newStage;
        $deal->save();

        // Log activity
        $deal->logActivity('stage_change', "Moved from {$oldStage} to {$newStage}");

        session()->flash('success', 'Deal stage updated successfully!');
    }

    public function getStageDeals($stage)
    {
        return Sponsorship::where('stage', $stage)
            ->with('user')
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    public function getStageValue($stage)
    {
        return Sponsorship::where('stage', $stage)->sum('value');
    }

    public function render()
    {
        $dealsByStage = [];
        $stageTotals = [];
        $stageCounts = [];

        foreach ($this->stages as $stage) {
            $deals = $this->getStageDeals($stage);
            $dealsByStage[$stage] = $deals;
            $stageTotals[$stage] = $deals->sum('value');
            $stageCounts[$stage] = $deals->count();
        }

        return view('livewire.sponsorships.index', [
            'dealsByStage' => $dealsByStage,
            'stageTotals' => $stageTotals,
            'stageCounts' => $stageCounts,
        ]);
    }
}
