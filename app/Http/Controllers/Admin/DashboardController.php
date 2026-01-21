<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Evidence;
use App\Models\LLMUsage;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_claims' => Claim::count(),
            'total_evidence' => Evidence::count(),
            'total_votes' => Vote::count(),
            'claims_this_month' => Claim::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->count(),
            'evidence_pending' => Evidence::where('status', 'PENDING')->count(),
            'evidence_ready' => Evidence::where('status', 'READY')->count(),
            'evidence_failed' => Evidence::where('status', 'FAILED')->count(),
        ];

        $recentClaims = Claim::with('creator')
            ->latest()
            ->limit(10)
            ->get();

        $recentEvidence = Evidence::with(['claim', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $llmCosts = LLMUsage::selectRaw('DATE(created_at) as date, SUM(cost) as daily_cost')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $verdictBreakdown = Claim::selectRaw('verdict, COUNT(*) as count')
            ->groupBy('verdict')
            ->get()
            ->pluck('count', 'verdict');

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentClaims' => $recentClaims,
            'recentEvidence' => $recentEvidence,
            'llmCosts' => $llmCosts,
            'verdictBreakdown' => $verdictBreakdown,
        ]);
    }
}
