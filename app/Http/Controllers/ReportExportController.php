<?php

namespace App\Http\Controllers;

use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    protected function getDateRange(Request $request)
    {
        $dateRange = $request->get('dateRange', 'this_quarter');
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');

        if ($dateRange === 'custom' && $customStartDate && $customEndDate) {
            return [
                'start' => \Carbon\Carbon::parse($customStartDate)->startOfDay(),
                'end' => \Carbon\Carbon::parse($customEndDate)->endOfDay(),
            ];
        }

        return match($dateRange) {
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

    public function exportCSV(Request $request): StreamedResponse
    {
        $dates = $this->getDateRange($request);
        $query = Sponsorship::with('user')
            ->whereBetween('created_at', [$dates['start'], $dates['end']]);

        if ($request->filled('selectedUser')) {
            $query->where('user_id', $request->get('selectedUser'));
        }
        if ($request->filled('selectedTier')) {
            $query->where('tier', $request->get('selectedTier'));
        }
        if ($request->filled('selectedStage')) {
            $query->where('stage', $request->get('selectedStage'));
        }

        $deals = $query->get();
        $filename = 'sponsorship_report_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($deals) {
            $output = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($output, ['Company', 'Contact Name', 'Email', 'Tier', 'Stage', 'Value', 'Priority', 'Source', 'Owner', 'Created Date', 'Last Activity']);
            
            foreach ($deals as $deal) {
                fputcsv($output, [
                    $deal->company_name,
                    $deal->decision_maker_name ?? '',
                    $deal->decision_maker_email ?? '',
                    $deal->tier,
                    $deal->stage,
                    $deal->value,
                    $deal->priority,
                    $deal->source ?? '',
                    $deal->user->name ?? '',
                    $deal->created_at->format('Y-m-d'),
                    $deal->last_activity_at?->format('Y-m-d') ?? ''
                ]);
            }
            
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPipelineReport(Request $request): StreamedResponse
    {
        $dates = $this->getDateRange($request);
        
        $pipelineData = Sponsorship::select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('stage')
            ->get();

        $filename = 'pipeline_report_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($pipelineData, $dates) {
            $output = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title row
            fputcsv($output, ['Pipeline Report - ' . $dates['start']->format('M d, Y') . ' to ' . $dates['end']->format('M d, Y')]);
            fputcsv($output, []); // Empty row
            
            // Header row
            fputcsv($output, ['Stage', 'Deal Count', 'Total Value']);
            
            foreach ($pipelineData as $stage) {
                fputcsv($output, [
                    $stage->stage,
                    $stage->count,
                    number_format($stage->total_value ?? 0, 2)
                ]);
            }
            
            fputcsv($output, []); // Empty row
            fputcsv($output, ['Total', $pipelineData->sum('count'), number_format($pipelineData->sum('total_value'), 2)]);
            
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPerformanceReport(Request $request): StreamedResponse
    {
        $dates = $this->getDateRange($request);
        
        $performanceData = Sponsorship::select('user_id', DB::raw('COUNT(*) as deal_count'), DB::raw('SUM(value) as total_value'))
            ->with('user')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('user_id')
            ->orderByDesc('total_value')
            ->get();

        $filename = 'performance_report_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($performanceData, $dates) {
            $output = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title row
            fputcsv($output, ['Performance Report - ' . $dates['start']->format('M d, Y') . ' to ' . $dates['end']->format('M d, Y')]);
            fputcsv($output, []); // Empty row
            
            // Header row
            fputcsv($output, ['Team Member', 'Deal Count', 'Total Value', 'Average Deal Size']);
            
            foreach ($performanceData as $perf) {
                $avgDeal = $perf->deal_count > 0 ? ($perf->total_value ?? 0) / $perf->deal_count : 0;
                fputcsv($output, [
                    $perf->user->name ?? 'Unknown',
                    $perf->deal_count,
                    number_format($perf->total_value ?? 0, 2),
                    number_format($avgDeal, 2)
                ]);
            }
            
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
