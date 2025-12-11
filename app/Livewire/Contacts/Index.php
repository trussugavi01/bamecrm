<?php

namespace App\Livewire\Contacts;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Contacts - B.A.M.E CRM')]
class Index extends Component
{
    public $search = '';
    public $tierFilter = '';
    public $stageFilter = '';
    
    public $showDetailModal = false;
    public $selectedDeal = null;

    public function viewDeal($dealId)
    {
        $this->selectedDeal = Sponsorship::with(['user', 'activities' => function($q) {
            $q->latest()->limit(5);
        }])->find($dealId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedDeal = null;
    }

    public function render()
    {
        $query = Sponsorship::query()
            ->whereNotNull('decision_maker_name')
            ->whereNotNull('decision_maker_email');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('decision_maker_name', 'like', '%' . $this->search . '%')
                  ->orWhere('decision_maker_email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tierFilter) {
            $query->where('tier', $this->tierFilter);
        }

        if ($this->stageFilter) {
            $query->where('stage', $this->stageFilter);
        }

        $contacts = $query->with('user')->latest()->get();

        // Group by company
        $groupedContacts = $contacts->groupBy('company_name');

        return view('livewire.contacts.index', [
            'groupedContacts' => $groupedContacts,
            'totalContacts' => $contacts->count(),
        ]);
    }
}
