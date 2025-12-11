<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\Sponsorship;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Dashboard - B.A.M.E CRM')]
class Dashboard extends Component
{
    public $pipelineHealth = 0;
    public $winLossRate = 0;
    public $newDealsCount = 0;
    public $quarterlyGoal = 1600000; // £1.6M
    public $funnelData = [];
    public $dateRange = 'quarter';
    
    // New metrics
    public $totalDeals = 0;
    public $activeDeals = 0;
    public $closedWonDeals = 0;
    public $closedLostDeals = 0;
    public $totalPipelineValue = 0;
    public $avgDealValue = 0;
    public $recentDeals = [];
    public $upcomingFollowUps = [];
    public $stagnantDeals = [];
    public $dealsByTier = [];
    public $dealsByPriority = [];
    public $recentActivities = [];
    public $monthlyTrend = [];
    public $conversionRate = 0;
    public $pipelineProgress = 0;
    
    // Modal properties
    public $selectedDeal = null;
    public $showModal = false;

    public function mount()
    {
        $this->calculateMetrics();
    }
    
    public function openDeal($dealId)
    {
        $this->selectedDeal = $dealId;
        $this->showModal = true;
    }
    
    #[On('close-modal')]
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDeal = null;
    }
    
    #[On('deal-updated')]
    public function refreshMetrics()
    {
        $this->calculateMetrics();
    }

    public function updatedDateRange()
    {
        $this->calculateMetrics();
    }

    public function calculateMetrics()
    {
        $dateStart = match($this->dateRange) {
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfQuarter(),
        };
        $dateEnd = match($this->dateRange) {
            'month' => now()->endOfMonth(),
            'year' => now()->endOfYear(),
            default => now()->endOfQuarter(),
        };

        // Total counts
        $this->totalDeals = Sponsorship::count();
        $this->activeDeals = Sponsorship::whereNotIn('stage', ['Active Partnership', 'Closed Lost'])->count();
        $this->closedWonDeals = Sponsorship::where('stage', 'Active Partnership')->count();
        $this->closedLostDeals = Sponsorship::where('stage', 'Closed Lost')->count();

        // Pipeline Health: Sum of weighted values
        $this->pipelineHealth = Sponsorship::whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->get()
            ->sum('weighted_value');

        // Total pipeline value (unweighted)
        $this->totalPipelineValue = Sponsorship::whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->sum('value');

        // Average deal value
        $this->avgDealValue = $this->activeDeals > 0 
            ? $this->totalPipelineValue / $this->activeDeals 
            : 0;

        // Pipeline progress towards goal
        $this->pipelineProgress = $this->quarterlyGoal > 0 
            ? min(100, round(($this->pipelineHealth / $this->quarterlyGoal) * 100)) 
            : 0;

        // Funnel Data: Count by stage with values
        $this->funnelData = Sponsorship::select('stage', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->groupBy('stage')
            ->get()
            ->mapWithKeys(fn($item) => [$item->stage => [
                'count' => $item->count,
                'value' => $item->total_value ?? 0
            ]])
            ->toArray();

        // Win/Loss Rate
        $totalClosed = Sponsorship::whereIn('stage', ['Active Partnership', 'Closed Lost'])->count();
        $won = Sponsorship::where('stage', 'Active Partnership')->count();
        $this->winLossRate = $totalClosed > 0 ? round(($won / $totalClosed) * 100, 1) : 0;

        // Conversion rate (from Prospect to Active Partnership)
        $this->conversionRate = $this->totalDeals > 0 
            ? round(($this->closedWonDeals / $this->totalDeals) * 100, 1) 
            : 0;

        // New Deals (based on date range)
        $this->newDealsCount = Sponsorship::whereBetween('created_at', [$dateStart, $dateEnd])->count();

        // Recent deals (last 5)
        $this->recentDeals = Sponsorship::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($deal) => [
                'id' => $deal->id,
                'company_name' => $deal->company_name,
                'value' => $deal->value,
                'stage' => $deal->stage,
                'tier' => $deal->tier,
                'priority' => $deal->priority,
                'created_at' => $deal->created_at,
                'user_name' => $deal->user?->name ?? 'Unassigned',
            ])
            ->toArray();

        // Upcoming follow-ups (next 7 days)
        $this->upcomingFollowUps = Sponsorship::whereNotNull('next_follow_up_date')
            ->whereBetween('next_follow_up_date', [now(), now()->addDays(7)])
            ->orderBy('next_follow_up_date')
            ->take(5)
            ->get()
            ->map(fn($deal) => [
                'id' => $deal->id,
                'company_name' => $deal->company_name,
                'next_follow_up_date' => $deal->next_follow_up_date,
                'stage' => $deal->stage,
                'priority' => $deal->priority,
            ])
            ->toArray();

        // Stagnant deals (no activity in 14 days)
        $this->stagnantDeals = Sponsorship::whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->where('last_activity_at', '<', now()->subDays(14))
            ->orderBy('last_activity_at')
            ->take(5)
            ->get()
            ->map(fn($deal) => [
                'id' => $deal->id,
                'company_name' => $deal->company_name,
                'last_activity_at' => $deal->last_activity_at,
                'stage' => $deal->stage,
                'value' => $deal->value,
            ])
            ->toArray();

        // Deals by tier
        $this->dealsByTier = Sponsorship::select('tier', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->groupBy('tier')
            ->get()
            ->mapWithKeys(fn($item) => [$item->tier => [
                'count' => $item->count,
                'value' => $item->total_value ?? 0
            ]])
            ->toArray();

        // Deals by priority
        $this->dealsByPriority = Sponsorship::select('priority', DB::raw('count(*) as count'))
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(fn($item) => [$item->priority => $item->count])
            ->toArray();

        // Recent activities (last 10)
        $this->recentActivities = Activity::with(['sponsorship', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($activity) => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'created_at' => $activity->created_at,
                'sponsorship_name' => $activity->sponsorship?->company_name ?? 'Unknown',
                'sponsorship_id' => $activity->sponsorship_id,
                'user_name' => $activity->user?->name ?? 'System',
            ])
            ->toArray();

        // Monthly trend (last 6 months)
        $this->monthlyTrend = collect(range(5, 0))->map(function($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $created = Sponsorship::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $won = Sponsorship::where('stage', 'Active Partnership')
                ->whereYear('actual_close_date', $date->year)
                ->whereMonth('actual_close_date', $date->month)
                ->count();
            return [
                'month' => $date->format('M'),
                'created' => $created,
                'won' => $won,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
