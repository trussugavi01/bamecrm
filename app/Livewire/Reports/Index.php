<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\Sponsorship;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Reports - B.A.M.E CRM')]
class Index extends Component
{
    public $dateRange = 'this_quarter';
    public $reportType = 'overview';
    public $customStartDate = '';
    public $customEndDate = '';
    public $selectedUser = '';
    public $selectedTier = '';
    public $selectedStage = '';
    
    // Modal properties
    public $showSourceModal = false;
    public $selectedSource = null;
    public $sourceDetails = [];
    public $drillDownMetric = null;
    public $drillDownDeals = [];
    
    public function mount()
    {
        $this->customStartDate = now()->startOfQuarter()->format('Y-m-d');
        $this->customEndDate = now()->format('Y-m-d');
    }
    
    public function openSourceDetails($source)
    {
        $this->selectedSource = $source;
        $this->loadSourceDetails();
        $this->showSourceModal = true;
    }
    
    #[On('close-source-modal')]
    public function closeSourceModal()
    {
        $this->showSourceModal = false;
        $this->selectedSource = null;
        $this->sourceDetails = [];
        $this->drillDownMetric = null;
        $this->drillDownDeals = [];
    }
    
    public function showMetricDetails($metric)
    {
        $this->drillDownMetric = $metric;
        $this->loadDrillDownDeals();
    }
    
    public function closeDrillDown()
    {
        $this->drillDownMetric = null;
        $this->drillDownDeals = [];
    }
    
    protected function loadDrillDownDeals()
    {
        if (!$this->selectedSource || !$this->drillDownMetric) {
            return;
        }
        
        $dates = $this->getDateRange();
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        
        // Build base query
        $query = Sponsorship::where('source', $this->selectedSource)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user');
        
        // Apply filters
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }
        if ($this->selectedTier) {
            $query->where('tier', $this->selectedTier);
        }
        if ($this->selectedStage) {
            $query->where('stage', $this->selectedStage);
        }
        
        // Filter based on metric type
        switch ($this->drillDownMetric) {
            case 'total_deals':
                // All deals from this source
                $this->drillDownDeals = $query->orderByDesc('created_at')->get();
                break;
                
            case 'total_value':
                // Deals sorted by value
                $this->drillDownDeals = $query->orderByDesc('value')->get();
                break;
                
            case 'conversion_rate':
                // Won deals only
                $this->drillDownDeals = $query->where('stage', 'Active Partnership')->orderByDesc('actual_close_date')->get();
                break;
                
            case 'avg_deal_value':
                // All deals sorted by value to show the average context
                $this->drillDownDeals = $query->orderByDesc('value')->get();
                break;
                
            default:
                $this->drillDownDeals = collect();
        }
    }
    
    protected function loadSourceDetails()
    {
        if (!$this->selectedSource) {
            return;
        }
        
        $dates = $this->getDateRange();
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        
        // Build base query
        $query = Sponsorship::where('source', $this->selectedSource)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply filters
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }
        if ($this->selectedTier) {
            $query->where('tier', $this->selectedTier);
        }
        if ($this->selectedStage) {
            $query->where('stage', $this->selectedStage);
        }
        
        // Get detailed metrics
        $totalDeals = $query->count();
        $totalValue = $query->sum('value');
        $avgDealValue = $totalDeals > 0 ? $totalValue / $totalDeals : 0;
        $wonDeals = (clone $query)->where('stage', 'Active Partnership')->count();
        $lostDeals = (clone $query)->where('stage', 'Closed Lost')->count();
        $activeDeals = (clone $query)->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])->count();
        $conversionRate = $totalDeals > 0 ? round(($wonDeals / $totalDeals) * 100, 1) : 0;
        
        // Deals by stage
        $dealsByStage = (clone $query)
            ->select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->groupBy('stage')
            ->orderByDesc('count')
            ->get();
        
        // Deals by tier
        $dealsByTier = (clone $query)
            ->select('tier', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->groupBy('tier')
            ->orderByDesc('total_value')
            ->get();
        
        // Deals by priority
        $dealsByPriority = (clone $query)
            ->select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();
        
        // Top deals from this source
        $topDeals = (clone $query)
            ->with('user')
            ->orderByDesc('value')
            ->limit(5)
            ->get();
        
        // Recent deals from this source
        $recentDeals = (clone $query)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Monthly trend for this source
        $monthlyTrend = (clone $query)
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(value) as total_value')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $this->sourceDetails = [
            'totalDeals' => $totalDeals,
            'totalValue' => $totalValue,
            'avgDealValue' => $avgDealValue,
            'wonDeals' => $wonDeals,
            'lostDeals' => $lostDeals,
            'activeDeals' => $activeDeals,
            'conversionRate' => $conversionRate,
            'dealsByStage' => $dealsByStage,
            'dealsByTier' => $dealsByTier,
            'dealsByPriority' => $dealsByPriority,
            'topDeals' => $topDeals,
            'recentDeals' => $recentDeals,
            'monthlyTrend' => $monthlyTrend,
        ];
    }

    public function updatedDateRange()
    {
        // Update custom dates when preset is selected
        if ($this->dateRange !== 'custom') {
            $dates = $this->getDateRange();
            $this->customStartDate = $dates['start']->format('Y-m-d');
            $this->customEndDate = $dates['end']->format('Y-m-d');
        }
    }

    protected function getDateRange()
    {
        if ($this->dateRange === 'custom') {
            return [
                'start' => \Carbon\Carbon::parse($this->customStartDate)->startOfDay(),
                'end' => \Carbon\Carbon::parse($this->customEndDate)->endOfDay(),
            ];
        }

        return match($this->dateRange) {
            'this_week' => ['start' => now()->startOfWeek(), 'end' => now()],
            'this_month' => ['start' => now()->startOfMonth(), 'end' => now()],
            'this_quarter' => ['start' => now()->startOfQuarter(), 'end' => now()],
            'this_year' => ['start' => now()->startOfYear(), 'end' => now()],
            'last_month' => ['start' => now()->subMonth()->startOfMonth(), 'end' => now()->subMonth()->endOfMonth()],
            'last_quarter' => ['start' => now()->subQuarter()->startOfQuarter(), 'end' => now()->subQuarter()->endOfQuarter()],
            'last_year' => ['start' => now()->subYear()->startOfYear(), 'end' => now()->subYear()->endOfYear()],
            default => ['start' => now()->startOfQuarter(), 'end' => now()],
        };
    }

    public function exportCSV()
    {
        $dates = $this->getDateRange();
        $query = Sponsorship::with('user')
            ->whereBetween('created_at', [$dates['start'], $dates['end']]);

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }
        if ($this->selectedTier) {
            $query->where('tier', $this->selectedTier);
        }
        if ($this->selectedStage) {
            $query->where('stage', $this->selectedStage);
        }

        $deals = $query->get();

        $csv = "Company,Contact Name,Email,Tier,Stage,Value,Priority,Source,Owner,Created Date,Last Activity\n";
        
        foreach ($deals as $deal) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s",%s,"%s","%s","%s","%s","%s"' . "\n",
                str_replace('"', '""', $deal->company_name),
                str_replace('"', '""', $deal->decision_maker_name ?? ''),
                str_replace('"', '""', $deal->decision_maker_email ?? ''),
                $deal->tier,
                $deal->stage,
                $deal->value,
                $deal->priority,
                str_replace('"', '""', $deal->source ?? ''),
                str_replace('"', '""', $deal->user->name ?? ''),
                $deal->created_at->format('Y-m-d'),
                $deal->last_activity_at?->format('Y-m-d') ?? ''
            );
        }

        $filename = 'sponsorship_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPipelineReport()
    {
        $dates = $this->getDateRange();
        
        $pipelineData = Sponsorship::select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('stage')
            ->get();

        $csv = "Pipeline Report - " . $dates['start']->format('M d, Y') . " to " . $dates['end']->format('M d, Y') . "\n\n";
        $csv .= "Stage,Deal Count,Total Value\n";
        
        foreach ($pipelineData as $stage) {
            $csv .= sprintf("%s,%d,%.2f\n", $stage->stage, $stage->count, $stage->total_value ?? 0);
        }

        $csv .= sprintf("\nTotal,%d,%.2f\n", $pipelineData->sum('count'), $pipelineData->sum('total_value'));

        $filename = 'pipeline_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPerformanceReport()
    {
        $dates = $this->getDateRange();
        
        $performanceData = Sponsorship::select('user_id', DB::raw('COUNT(*) as deal_count'), DB::raw('SUM(value) as total_value'))
            ->with('user')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('user_id')
            ->orderByDesc('total_value')
            ->get();

        $csv = "Performance Report - " . $dates['start']->format('M d, Y') . " to " . $dates['end']->format('M d, Y') . "\n\n";
        $csv .= "Team Member,Deal Count,Total Value,Average Deal Size\n";
        
        foreach ($performanceData as $perf) {
            $avgDeal = $perf->deal_count > 0 ? ($perf->total_value ?? 0) / $perf->deal_count : 0;
            $csv .= sprintf(
                '"%s",%d,%.2f,%.2f' . "\n",
                $perf->user->name ?? 'Unknown',
                $perf->deal_count,
                $perf->total_value ?? 0,
                $avgDeal
            );
        }

        $filename = 'performance_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    
    public function render()
    {
        $dates = $this->getDateRange();
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        
        // Build base query with filters
        $baseQuery = Sponsorship::query();
        if ($this->selectedUser) {
            $baseQuery->where('user_id', $this->selectedUser);
        }
        if ($this->selectedTier) {
            $baseQuery->where('tier', $this->selectedTier);
        }
        if ($this->selectedStage) {
            $baseQuery->where('stage', $this->selectedStage);
        }
        
        // Pipeline by Stage
        $pipelineByStage = (clone $baseQuery)
            ->select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->groupBy('stage')
            ->get();
        
        // Deals by Source
        $dealsBySource = (clone $baseQuery)
            ->select('source', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();
        
        // Deals by Tier
        $dealsByTier = (clone $baseQuery)
            ->select('tier', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tier')
            ->get();
        
        // Monthly Trend
        $monthlyTrend = (clone $baseQuery)
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(value) as total_value')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Win/Loss Analysis
        $wonDeals = (clone $baseQuery)
            ->where('stage', 'Active Partnership')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $lostDeals = (clone $baseQuery)
            ->where('stage', 'Closed Lost')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        
        // Total pipeline value
        $totalPipelineValue = (clone $baseQuery)
            ->whereNotIn('stage', ['Closed Lost', 'Active Partnership'])
            ->sum('value');
        
        // Weighted pipeline value
        $weightedPipelineValue = (clone $baseQuery)
            ->whereNotIn('stage', ['Closed Lost', 'Active Partnership'])
            ->selectRaw('SUM(value * probability / 100) as weighted')
            ->value('weighted') ?? 0;
        
        // Average Deal Size
        $avgDealSize = (clone $baseQuery)
            ->whereNotIn('stage', ['Closed Lost'])
            ->avg('value') ?? 0;
        
        // Deals in period
        $dealsInPeriod = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Value in period
        $valueInPeriod = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('value');
        
        // Conversion Rate by Stage
        $totalDeals = Sponsorship::count();
        $conversionRates = [];
        foreach(Sponsorship::STAGES as $stage) {
            $stageCount = Sponsorship::where('stage', $stage)->count();
            $conversionRates[$stage] = $totalDeals > 0 ? round(($stageCount / $totalDeals) * 100, 1) : 0;
        }
        
        // Top Performers (Users with most deals)
        $topPerformers = Sponsorship::select('user_id', DB::raw('COUNT(*) as deal_count'), DB::raw('SUM(value) as total_value'))
            ->with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();
        
        // Recent Activities
        $recentActivities = Activity::with(['sponsorship', 'user'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Stagnant deals
        $stagnantDeals = Sponsorship::whereNotIn('stage', ['Closed Won', 'Closed Lost', 'Active Partnership'])
            ->where('last_activity_at', '<', now()->subDays(14))
            ->count();
        
        // All deals for export table
        $allDeals = (clone $baseQuery)
            ->with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        // Users for filter
        $users = User::all();
        
        return view('livewire.reports.index', [
            'pipelineByStage' => $pipelineByStage,
            'dealsBySource' => $dealsBySource,
            'dealsByTier' => $dealsByTier,
            'monthlyTrend' => $monthlyTrend,
            'wonDeals' => $wonDeals,
            'lostDeals' => $lostDeals,
            'avgDealSize' => $avgDealSize,
            'conversionRates' => $conversionRates,
            'topPerformers' => $topPerformers,
            'recentActivities' => $recentActivities,
            'totalPipelineValue' => $totalPipelineValue,
            'weightedPipelineValue' => $weightedPipelineValue,
            'dealsInPeriod' => $dealsInPeriod,
            'valueInPeriod' => $valueInPeriod,
            'stagnantDeals' => $stagnantDeals,
            'allDeals' => $allDeals,
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stages' => Sponsorship::STAGES,
            'tiers' => Sponsorship::TIERS,
        ]);
    }
}
